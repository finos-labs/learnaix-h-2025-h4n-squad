<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace block_ai_chat\local;

use moodle_page;

/**
 * Class helper
 *
 * @package    block_ai_chat
 * @copyright  2024 Tobias Garske, ISB Bayern
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class Helper {
    /**
     * Call the Java microservice with a prompt and return the response.
     *
     * @param string $prompt The user prompt to send to the microservice.
     * @return string JSON encoded response or error.
     */
    public static function call_java_microservice(string $prompt): string {
        $url = 'http://localhost:8080/generateSql';
        $request_body = [
            'question' => $prompt
        ];
        $headers = [
            'Content-Type: application/json',
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlerr = curl_error($ch);
        curl_close($ch);

        if ($result === false) {
            return json_encode(['error' => 'CURL failed: ' . $curlerr]);
        }
        if ($httpcode !== 200) {
            return json_encode(['error' => 'Java microservice returned status ' . $httpcode, 'response' => $result]);
        }

        $json = json_decode($result, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return json_encode(['error' => 'JSON parse error: ' . json_last_error_msg(), 'raw_response' => $result]);
        }

        // Try to extract the reply text (adjust as needed for your Java service's response)
        $reply = '';
        if (isset($json['sql'])) {
            $reply = $json['sql'];
        } elseif (isset($json['response'])) {
            $reply = $json['response'];
        } elseif (isset($json['message'])) {
            $reply = $json['message'];
        } else {
            $reply = json_encode($json);
        }

        return json_encode(['reply' => $reply]);
    }

    /**
     * Generate a JWT for Snowflake authentication (placeholder, use a real JWT library in production).
     *
     * @param string $account
     * @param string $user
     * @param string $private_key
     * @return string|false
     */
    private static function generateSnowflakeJwt(string $account, string $user, string $private_key): string|false {
        // Requires firebase/php-jwt. Install via Composer:
        // composer require firebase/php-jwt
        $result = false;
        try {
            if (!class_exists('Firebase\JWT\JWT')) {
                // Not installed.
                return $result;
            }
            $now = time();
            $payload = [
                'iss' => $user,
                'sub' => $user,
                'iat' => $now,
                'exp' => $now + 3600,
                'aud' => $account,
            ];
            // Convert PKCS#8 PEM to usable format if needed.
            $privateKey = openssl_pkey_get_private($private_key);
            if ($privateKey) {
                return \Firebase\JWT\JWT::encode($payload, $private_key, 'RS256');
            }
        } catch (\Exception $e) {
            // Do nothing, return false below
        }
        return $result;
    }
    /**
     * Check, if a block is existing in course context.
     * @param int $courseid
     * @return object|bool
     * @throws \dml_exception
     */
    public static function hasBlockInCourseContext(int $courseid): object|bool {
        global $DB;

        // Check if tenant is enabled for the school.
        $sql = "SELECT bi.*
                  FROM {block_instances} bi
                  JOIN {context} ctx ON bi.parentcontextid = ctx.id
                 WHERE bi.blockname = :blockname AND ctx.contextlevel = :contextlevel
                   AND ctx.instanceid = :courseid";

        $params = [
            'blockname' => 'ai_chat',
            'contextlevel' => CONTEXT_COURSE,
            'courseid' => $courseid,
        ];

        return $DB->get_record_sql($sql, $params);
    }

    /**
     * Helper function to determine if the global instance floating button should be shown.
     *
     * @param moodle_page $page The page needed to determine if the global instance should be rendered
     * @return bool true if the global instance floating button should be rendered or not
     */
    public static function showGlobalBlock(moodle_page $page): bool {
        $shouldShow = false;
        if (isloggedin() && !$page->blocks->is_block_present('ai_chat')) {
            $showonpagetypes = get_config('block_ai_chat', 'showonpagetypes');
            if (trim($showonpagetypes) === '*') {
                $shouldShow = true;
            } elseif (!empty(trim($showonpagetypes))) {
                $pagetypes = array_filter(array_map('trim', explode(PHP_EOL, $showonpagetypes)));
                foreach ($pagetypes as $value) {
                    if (str_starts_with($page->pagetype, $value)) {
                        $shouldShow = true;
                        break;
                    }
                }
            }
        }
        return $shouldShow;
    }
}

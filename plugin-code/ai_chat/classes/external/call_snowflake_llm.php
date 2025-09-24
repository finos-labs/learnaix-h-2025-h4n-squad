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

namespace block_ai_chat\external;

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;
use block_ai_chat\local\Helper;

require_once $CFG->libdir . '/externallib.php';

class CallSnowflakeLlm extends external_api {
    public static function execute_parameters() {
        return new external_function_parameters([
            'prompt' => new external_value(PARAM_TEXT, 'Prompt for the LLM'),
            'options' => new external_value(PARAM_RAW, 'Options as JSON', VALUE_DEFAULT, '{}'),
        ]);
    }

    public static function execute($prompt, $options = '{}') {
        $opts = json_decode($options, true) ?: [];
        $result = Helper::call_snowflake_llm($prompt, $opts);
        return [
            'result' => $result,
            'code' => $result ? 200 : 500,
        ];
    }

    public static function execute_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_RAW, 'LLM response'),
            'code' => new external_value(PARAM_INT, 'Status code'),
        ]);
    }
}

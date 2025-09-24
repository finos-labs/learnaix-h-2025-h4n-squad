<?php
/*
 * Copyright 2025 FINOS
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

use local_ai_manager\ai_manager_utils;
use local_ai_manager\local\userinfo;

/**
 * Block class for block_ai_chat
 *
 * @package    block_ai_chat
 * @copyright  2024 ISB Bayern
 * @author     Tobias Garske
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_ai_chat extends block_base {

    /**
     * Returns the default region for this block.
     *
     * @return string
     */
    public function get_default_region() {
        return 'side-pre';
    }

    /**
     * Initialize block
     *
     * @return void
     * @throws coding_exception
     */
    public function init(): void {
        $this->title = get_string('ai_chat', 'block_ai_chat');
    }

    /**
     * Allow the block to have a configuration page
     *
     * @return bool
     */
    #[\Override]
    public function has_config(): bool {
        return true;
    }

    /**
     * Returns the block content. Content is cached for performance reasons.
     *
     * @return stdClass
     * @throws coding_exception
     * @throws moodle_exception
     */
    #[\Override]
    public function get_content(): stdClass {
        global $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        $context = \context_block::instance($this->instance->id);
        if (!has_capability('block/ai_chat:view', $context)) {
            return $this->content;
        }

        // We retrieve the config for all the purposes we are using. This includes the purposes that tiny_ai uses, because even
        // if the chat purpose is not available, the user should still be able to use the chatbot for accessing the tiny_ai tools.
        $aiconfig = ai_manager_utils::get_ai_config($USER, $context->id, null,
                ['chat', 'singleprompt', 'translate', 'itt', 'imggen', 'tts']);
        if ($aiconfig['availability']['available'] === ai_manager_utils::AVAILABILITY_HIDDEN) {
            return $this->content;
        }
        $atleastonepurposenothidden =
                array_reduce($aiconfig['purposes'], fn($a, $b) => $a || $b['available'] !== ai_manager_utils::AVAILABILITY_HIDDEN,
                        false);
        if (!$atleastonepurposenothidden) {
            return $this->content;
        }
        $this->content = new stdClass;

        // Add button that will trigger the chat
        $this->content->text = '<button class="btn btn-primary" onclick="showAIChat()">Open Chat</button>';
        
        // Add the chat UI HTML
        $this->content->text .= '
        <div id="ai-chat-modal" style="display: none; position: fixed; bottom: 80px; right: 20px; width: 350px; height: 500px; background: white; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); z-index: 1000;">
            <div style="padding: 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                <h5 style="margin: 0;">AI Chat</h5>
                <button class="btn btn-link" onclick="hideAIChat()">×</button>
            </div>
            <div id="ai-chat-messages" style="height: 360px; overflow-y: auto; padding: 15px;"></div>
            <div style="padding: 15px; border-top: 1px solid #eee;">
                <textarea id="ai-chat-input" class="form-control mb-2" rows="2"></textarea>
                <button class="btn btn-primary" onclick="sendMessage()">Send</button>
            </div>
        </div>';

        // Add the JavaScript
        $js = "
        window.showAIChat = function() {
            document.getElementById('ai-chat-modal').style.display = 'block';
            // Get initial welcome message
            fetch('https://resedaceous-charlsie-unvenially.ngrok-free.dev/app/execute')
                .then(response => response.text())
                .then(data => {
                    appendMessage(data, 'assistant');
                });
        }

        window.hideAIChat = function() {
            document.getElementById('ai-chat-modal').style.display = 'none';
        }

        window.appendMessage = function(text, sender) {
            const messagesDiv = document.getElementById('ai-chat-messages');
            const messageDiv = document.createElement('div');
            messageDiv.className = 'alert ' + (sender === 'user' ? 'alert-secondary' : 'alert-info');
            messageDiv.style.marginBottom = '10px';
            messageDiv.textContent = text;
            messagesDiv.appendChild(messageDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        window.sendMessage = function() {
            const input = document.getElementById('ai-chat-input');
            const text = input.value.trim();
            if (!text) return;

            appendMessage(text, 'user');
            input.value = '';

            fetch('https://resedaceous-charlsie-unvenially.ngrok-free.dev/getPromptResponse', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({prompt: text})
            })
            .then(response => response.text())
            .then(data => {
                appendMessage(data, 'assistant');
            });
        }

        // Allow Enter key to send message
        document.getElementById('ai-chat-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });";

        $this->page->requires->js_init_code($js);

        if ($this->page->user_is_editing()) {
            return $this->content;
        }

        return $this->content;
    }

    /**
     * Returns false as there can be only one ai_chat block on one page to avoid collisions.
     *
     * @return bool
     */
    #[\Override]
    public function instance_allow_multiple(): bool {
        return false;
    }

    /**
     * Returns on which page formats this block can be added.
     *
     * We do not want any user to create the block manually.
     * But me must add at least one applicable format here otherwise it will lead to an installation error,
     * because the block::_self_test fails.
     *
     * There are only two ways to create block instances:
     * - Check "add ai block" in the settings form of a course
     * - Admin has set up an automatic create of a block instance using the plugin settings.
     *
     * @return array
     */
    #[\Override]
    public function applicable_formats(): array {
        return ['course-view' => true];
    }

    /**
     * We don't want any user to manually create an instance of this block.
     *
     * @param $page
     * @return false
     */
    #[\Override]
    public function user_can_addto($page) {
        return false;
    }

    /**
     *  Do any additional initialization you may need at the time a new block instance is created
     *
     * @return boolean
     * /
     * @return true
     * @throws dml_exception
     */
    #[\Override]
    public function instance_create() {
        global $DB;

        // For standard dashboard keep the standard.
        if (isset($this->page->context) && $this->page->context::instance()->id != SYSCONTEXTID) {
            return true;
        }

        // For courses set default to show on all pages.
        if ($this->context->get_parent_context()->contextlevel === CONTEXT_COURSE) {
            $DB->update_record('block_instances', ['id' => $this->instance->id, 'pagetypepattern' => '*']);
        }
        return true;
    }

}

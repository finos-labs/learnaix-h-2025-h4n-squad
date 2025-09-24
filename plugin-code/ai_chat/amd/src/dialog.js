// This file is part of Moodle - http://moodle.org/

/**
 * JavaScript functions for AI Chat.
 *
 * @module     block_ai_chat/dialog
 * @copyright  2023 Basil
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/modal_factory'], function($, ModalFactory) {
    'use strict';

    return {
        init: function() {
            var template = '<div class="p-3">' +
                '<div class="chat-messages mb-3" style="max-height: 300px; overflow-y: auto;"></div>' +
                '<textarea class="form-control mb-2" rows="3"></textarea>' +
                '<button class="btn btn-primary">Send</button></div>';

            ModalFactory.create({
                title: 'AI Chat',
                body: template
            }).then(function(modal) {
                modal.show();
                var root = modal.getRoot();
                var messagesDiv = root.find('.chat-messages');

                // Initial welcome message
                $.ajax({
                    url: 'https://resedaceous-charlsie-unvenially.ngrok-free.dev/app/execute',
                    method: 'GET',
                    success: function(data) {
                        messagesDiv.append('<div class="alert alert-info">' + data + '</div>');
                    }
                });

                root.find('button').click(function() {
                    var text = root.find('textarea').val();
                    if (!text) {
                        return;
                    }

                    // Show user message
                    messagesDiv.append('<div class="alert alert-secondary">' + text + '</div>');
                    root.find('textarea').val('');

                    // Send to API
                    $.ajax({
                        url: 'https://resedaceous-charlsie-unvenially.ngrok-free.dev/getPromptResponse',
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({prompt: text}),
                        success: function(data) {
                            messagesDiv.append('<div class="alert alert-info">' + data + '</div>');
                            messagesDiv.scrollTop(messagesDiv[0].scrollHeight);
                        }
                    });
                });
            });
        }
    };
});

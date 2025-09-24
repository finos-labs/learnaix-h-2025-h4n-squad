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

import {call as fetchMany} from 'core/ajax';

/**
 * Get all converstations a User can see.
 * @param {int} userid
 * @param {int} contextid
 * @returns {mixed}
 */
export const getAllConversations = (
    userid,
    contextid,
) => fetchMany([{
    methodname: 'block_ai_chat_get_all_conversations',
    args: {
        userid,
        contextid
}}])[0];

/**
 * Get all converstations a User can see.
 * @param {int} contextid
 * @returns {mixed}
 */
export const getNewConversationId = (
    contextid,
) => fetchMany([{
    methodname: 'block_ai_chat_get_new_conversation_id',
    args: {
        contextid,
}}])[0];

/**
 * Get all converstations a User can see.
 * @param {int} contextid
 * @param {int} userid
 * @param {int} conversationid
 * @returns {mixed}
 */
export const deleteConversation = (
    contextid,
    userid,
    conversationid,
) => fetchMany([{
    methodname: 'block_ai_chat_delete_conversation',
    args: {
        contextid,
        userid,
        conversationid,
}}])[0];

/**
 * Get conversationcontext message limit.
 * @param {int} contextid
 * @returns {mixed}
 */
export const getConversationcontextLimit = (
    contextid,
) => fetchMany([{
    methodname: 'block_ai_chat_get_conversationcontext_limit',
    args: {
        contextid
    }
}])[0];


/**
 * Get current persona.
 * @param {int} contextid
 * @returns {mixed}
 */
export const reloadPersona = (
    contextid,
) => fetchMany([{
    methodname: 'block_ai_chat_reload_persona',
    args: {
        contextid
    }
}])[0];

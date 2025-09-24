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
 * Make a request to the Snowflake LLM webservice.
 * @param {string} purpose
 * @param {string} prompt
 * @param {string} blockname
 * @param {number} contextid
 * @param {object} options
 * @returns {Promise<object>} LLM response
 */
export const makeRequest = async(purpose, prompt, blockname, contextid, options = {}) => {
    // Only handle chat for now; extend as needed.
    if (purpose !== 'chat') {
        throw new Error('Only chat purpose is supported for Snowflake LLM');
    }
    const args = {
        prompt: prompt,
        options: JSON.stringify(options || {})
    };
    const [result] = await fetchMany([{
        methodname: 'block_ai_chat_call_snowflake_llm',
        args: args
    }]);
    return result;
};

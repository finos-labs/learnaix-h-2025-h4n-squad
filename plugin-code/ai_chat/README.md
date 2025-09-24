# # 📬 Hai Assistant - Moodle AI Chat Plugin

## 📄 Summary of Your Solution

Our AI Assistant 'Hai' integrates directly into Moodle LMS to provide contextual help and support. Students and teachers no longer need to navigate to external websites for AI assistance.

### Problem Solved
Students and teachers lack an integrated AI Assistant within Moodle LMS for learning support and development. Hai provides direct access to course-related information and assistance within the Moodle environment.

### How it Works
- Integrates as a Moodle block plugin
- Provides floating chat interface
- Connects to secure AI backend
- Contextually aware of Moodle courses
- Helps with both course content and Moodle administration

### Technologies Used
- PHP 8.0+
- JavaScript/jQuery
- Moodle Plugin API
- Bootstrap UI
- REST API Integration

## 👥 Team Information

| Field            | Details                               |
| ---------------- | ------------------------------------- |
| Team Name        | H4N Squad                            |
| Title           | Hai Assistant                        |
| Theme           | AI companion                         |
| Contact Email   | basilmon92@gmail.com                 |
| Participants    | Basil M Kuriakose, Tanushree Chauhan, Priti Pathak, Panindra S, Rajeswari Murukesan |
| GitHub Usernames | @basilmon92, @tanushreechauhan-06, @pritipathak91-ai, @pani-init, @rajeswarimurukesan |

## License

Copyright 2025 FINOS

Distributed under the [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0).

SPDX-License-Identifier: [Apache-2.0](https://spdx.org/licenses/Apache-2.0) - Chat Frontend for local_ai_manager

This plugin provides a Frontend to converse with defined Ai´s from local_ai_manager.
Features are different viewmodes and a chat history.

# Settings

## Requirements

https://github.com/mebis-lp/moodle-local_ai_manager needs to be installed.

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/blocks/ai_chat

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## License ##

2024, ISB Bayern

Lead developer: Tobias Garske <tobias.garske@isb.bayern.de>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <https://www.gnu.org/licenses/>.

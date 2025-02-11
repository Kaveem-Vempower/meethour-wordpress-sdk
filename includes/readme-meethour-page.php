<?php

function meethour_readme_page()
{
?>
    <style>
        /* .nav-tab-active {
            float: left;
            border: 1px solid #c3c4c7;
            border-bottom: none;
            margin-left: .5em;
            padding: 5px 10px;
            font-size: 14px;
            line-height: 1.71428571;
            font-weight: 600;
            background: #dcdcde;
            color: #50575e;
            text-decoration: none;
            white-space: nowrap;
        } */
        .points {
            font-size: small;
        }

        .content {
            background: white;
            padding: 10px 20px;
            margin: 10px 0px;
            border-radius: 4px;
            width: 96%;
        }

        ul {
            padding: 0px 20px;
            list-style-type: square;
            font-size: small;
        }

        p {
            font-size: medium;
        }


        /*start styles*/
        .accordion {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
        }

        .accordion__item {
            border: 1px solid #e5f3fa;
            border-radius: 10px;
            overflow: hidden;
        }

        .accordion__header {
            padding: 20px 25px;
            font-weight: bold;
            cursor: pointer;
            position: relative;
            font-size: medium;
        }

        .accordion__header::after {
            content: '';
            background: url(https://www.svgrepo.com/show/357035/angle-down.svg) no-repeat center;
            width: 20px;
            height: 20px;
            transition: .4s;
            display: inline-block;
            position: absolute;
            right: 20px;
            top: 20px;
            z-index: 1;
        }

        .accordion__header.active {
            background: #e5f3fa;
        }

        .accordion__header.active::after {
            transform: rotateX(180deg);
        }

        .accordion__item .accordion__content {
            padding: 0 0px;
            max-height: 0;
            transition: .5s;
            overflow: hidden;
        }

        #wpfooter {
            display: none;
        }
    </style>
    <div id="tabs">
        <h2 class="nav-tab-wrapper">
            <a href="#tab-1" class="nav-tab">Settings</a>
            <a href="#tab-2" class="nav-tab">Documentation</a>
        </h2>
        <div id="tab-1" class="tab-content">
            <?php
            meethour_token_page()
            ?>
        </div>
        <div id="tab-2" class="tab-content">
            <article class="svelte-17yrd8o">
                <div class="preview svelte-prhgxu">
                    <div class="tabs">
                        <div class="grid svelte-prhgxu">
                            <div class="content">
                                <h2 class="svelte-prhgxu">Description</h2>
                                <div class="section plugin-description svelte-xiu0ih">
                                    <p>Discover the power of video conferencing with Meet Hour. Learn what video conferencing is, explore its diverse applications across industries, and find out why Meet Hour stands out as your preferred choice. Explore key features, reliability, and seamless integration options for your technology stacks. Join the future of remote collaboration with Meet Hour.</p>

                                    <!-- Markdown Code -->

                                    <hr>
                                    <h2 id="getting-started">Getting Started</h2>
                                    <h3 id="1-activate-the-plugin">1. Activate the Plugin</h3>
                                    <p>After activating the plugin, head over to the <a href="https://portal.meethour.io/customer/developers">Meethour Developer Portal</a>. You&#39;ll need a developer account to access this page.</p>
                                    <ul>
                                        <li>
                                            <p class="points"><strong>Find Your Credentials</strong>: In the <strong>Developer</strong> section, locate your <strong>Client ID</strong>, <strong>Client Secret</strong>, and <strong>API Key</strong>.</p>
                                        </li>
                                        <li>
                                            <p class="points"><strong>Configure Plugin Settings</strong>: Go back to your WordPress dashboard and navigate to the plugin settings page.
                                            <ul>
                                                <li>
                                                    <p class="points">Insert the <strong>Client ID</strong>, <strong>Client Secret</strong>, and <strong>API Key</strong>.</p>
                                                </li>
                                                <li>
                                                    <p class="points">Click on <strong>Generate Access Token</strong>.</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                        <li>
                                            <p class="points"><strong>Unlock Features</strong>: Once you&#39;ve generated the access token, all the plugin&#39;s features will be available to you.</p>
                                        </li>
                                    </ul>
                                    <hr>
                                    <h2 id="user-management">User Management</h2>
                                    <h3 id="2-sync-and-manage-users">2. Sync and Manage Users</h3>
                                    <p>Navigate to the <strong>Users</strong> section in WordPress. With the plugin activated, you&#39;ll notice new options like <strong>Fetch Meethour Users</strong>.</p>
                                    <ul>
                                        <li>
                                            <p class="points"><strong>Fetch Meethour Users</strong>: Click this to import all your Meethour users into your WordPress database.</p>
                                        </li>
                                        <li>
                                            <p class="points"><strong>Two-Way Synchronization</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points">Actions you perform on users with the <strong>Meethour</strong> role in WordPress can also affect those users in the Meethour portal.</p>
                                                </li>
                                                <li>
                                                    <p class="points">You&#39;ll have the option to decide whether changes in WordPress should reflect on the Meethour portal.</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                        <li>
                                            <p class="points"><strong>Creating Users</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points">When you create a new user in WordPress, assign them the <strong>Meethour</strong> role.</p>
                                                </li>
                                                <li>
                                                    <p class="points">This will automatically create a corresponding user in the Meethour portal.</p>
                                                </li>
                                                <li>
                                                    <p class="points"><em>Note</em>: Synchronization won&#39;t work if the user isn&#39;t assigned the <strong>Meethour</strong> role.</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                        <li>
                                            <p class="points"><strong>Deleting Users</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points">When deleting a user with the <strong>Meethour</strong> role, you&#39;ll be prompted to choose whether to remove them from the Meethour portal as well.</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                    </ul>
                                    <hr>
                                    <h2 id="meetings">Meetings</h2>
                                    <h3 id="3-instant-meeting">3. Instant Meeting</h3>
                                    <p>Need a quick meeting without the fuss? The <strong>Instant Meeting</strong> feature is your go-to.</p>
                                    <ul>
                                        <li>
                                            <p class="points"><strong>Creating an Instant Meeting</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points">Provide a <strong>Meeting Name</strong> and <strong>Passcode</strong>.</p>
                                                </li>
                                                <li>
                                                    <p class="points">Click <strong>Create</strong>, and you&#39;re all set!</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                        <li>
                                            <p class="points"><strong>Shortcode</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points">After creating the meeting, click <strong>Copy Shortcode</strong>.</p>
                                                </li>
                                                <li>
                                                    <p class="points">Paste this shortcode into any post or page to embed the meeting directly on your site.</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                        <li>
                                            <p class="points"><strong>Link</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points">Copy the meeting link to share with participants.</p>
                                                </li>
                                                <li>
                                                    <p class="points">Paste it into your browser to join the meeting instantly.</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                    </ul>
                                    <h3 id="4-schedule-meeting">4. Schedule Meeting</h3>
                                    <p>For more detailed setups, use the <strong>Schedule Meeting</strong> option.</p>
                                    <ul>
                                        <li>
                                            <p class="points"><strong>Setting Up a Meeting</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points">Fill in the meeting details—date, time, agenda, and any other preferences.</p>
                                                </li>
                                                <li>
                                                    <p class="points">You&#39;ll have more customization options here compared to an instant meeting.</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                        <li>
                                            <p class="points"><strong>Publishing the Meeting</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points">Click <strong>Publish</strong> to create the meeting.</p>
                                                </li>
                                                <li>
                                                    <p class="points">You&#39;ll receive a <strong>Permalink</strong> to the meeting post.</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                        <li>
                                            <p class="points"><strong>Editing the Meeting</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points">After publishing, you&#39;ll be redirected to the <strong>Edit Meeting</strong> page.</p>
                                                </li>
                                                <li>
                                                    <p class="points">Any updates you make here will sync with the Meethour portal.</p>
                                                </li>
                                                <li>
                                                    <p class="points">The meeting link remains the same even after updates.</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                    </ul>
                                    <h3 id="5-manage-meetings">5. Manage Meetings</h3>
                                    <p>In the <strong>Meetings</strong> section, you can view all meetings created in WordPress and fetch meetings from the Meethour portal.</p>
                                    <ul>
                                        <li>
                                            <p class="points"><strong>Fetching Meetings</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points">Click on <strong>Fetch Meethour Meetings</strong>.</p>
                                                </li>
                                                <li>
                                                    <p class="points">Each click fetches 20 meetings from the portal.</p>
                                                </li>
                                                <li>
                                                    <p class="points">If you have more meetings (e.g., 100), click the button multiple times until all meetings are imported.</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                        <li>
                                            <p class="points"><strong>Meeting Details</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points">View <strong>Meeting ID</strong>, <strong>Duration</strong>, <strong>Agenda</strong>, <strong>Meeting Link</strong>, and <strong>External Meeting Link</strong>.</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                        <li>
                                            <p class="points"><strong>Joining Meetings</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points"><strong>Meeting Link</strong>: Opens the meeting within your WordPress site. Invited users will be automatically signed in.</p>
                                                </li>
                                                <li>
                                                    <p class="points"><strong>External Link</strong>: Opens the meeting in a new browser tab.</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                        <li>
                                            <p class="points"><strong>Shortcodes</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points">Copy the shortcode to embed the meeting in any post or page.</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                        <li>
                                            <p class="points"><strong>Managing Meetings</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points"><strong>Edit</strong>, <strong>Move to Trash</strong>, or <strong>Delete</strong> meetings.</p>
                                                </li>
                                                <li>
                                                    <p class="points">When deleting, you&#39;ll be asked if you want to remove the meeting from the Meethour portal as well.
                                                    <ul>
                                                        <li>
                                                            <p class="points"><strong>Agree</strong>: The meeting is archived and deleted from both WordPress and Meethour.</p>
                                                        </li>
                                                        <li>
                                                            <p class="points"><strong>Disagree</strong>: The meeting is removed from WordPress but remains on Meethour.</p>
                                                        </li>
                                                    </ul>
                                                    </p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                        <li>
                                            <p class="points"><strong>Sync Upcoming Meetings</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points">The <strong>Sync Upcoming Meetings from Meethour</strong> button fetches only upcoming meetings.</p>
                                                </li>
                                                <li>
                                                    <p class="points"><em>Important</em>: Only future meetings are synced with this option.</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                    </ul>
                                    <hr>
                                    <h2 id="recordings">Recordings</h2>
                                    <h3 id="6-manage-recordings">6. Manage Recordings</h3>
                                    <p>Access all your recordings in the <strong>Recordings</strong> section, and fetch new ones from the Meethour portal.</p>
                                    <ul>
                                        <li>
                                            <p class="points"><strong>Fetching Recordings</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points">Click <strong>Fetch Meethour Recordings</strong>.</p>
                                                </li>
                                                <li>
                                                    <p class="points">Each click imports 20 recordings.</p>
                                                </li>
                                                <li>
                                                    <p class="points">For more recordings, click multiple times as needed.</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                        <li>
                                            <p class="points"><strong>Recording Details</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points">View <strong>Recording Name</strong>, <strong>Duration</strong>, <strong>Size</strong>, <strong>Recording Link</strong>, and <strong>Recording Date</strong>.</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                        <li>
                                            <p class="points"><strong>Viewing Recordings</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points"><strong>Recording Link</strong>: Opens the recording within your WordPress site.</p>
                                                </li>
                                                <li>
                                                    <p class="points">You can integrate recordings with other plugins or embed them in posts.</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                        <li>
                                            <p class="points"><strong>Shortcodes</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points">Copy the shortcode to embed the recording in any post or page.</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                        <li>
                                            <p class="points"><strong>Deleting Recordings</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points">Choose whether to delete recordings from the Meethour portal when removing them from WordPress.</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                        <li>
                                            <p class="points"><strong>Refresh Shortcodes</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points">If a shortcode expires, use the <strong>Refresh Shortcode</strong> option to update it automatically.</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                    </ul>
                                    <hr>
                                    <h2 id="shortcodes">Shortcodes</h2>
                                    <h3 id="7-using-shortcodes">7. Using Shortcodes</h3>
                                    <p>Leverage shortcodes to integrate Meethour functionalities throughout your WordPress site.</p>
                                    <ul>
                                        <li>
                                            <p class="points"><strong>For Meetings</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points"><code>[meethour meeting_id=&quot;your_meeting_id&quot;]</code></p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                        <li>
                                            <p class="points"><strong>For Recordings</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points"><code>[meethour recording_id=&quot;your_recording_id&quot;]</code></p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                        <li>
                                            <p class="points"><strong>How to Use</strong>:
                                            <ul>
                                                <li>
                                                    <p class="points">Copy the relevant shortcode.</p>
                                                </li>
                                                <li>
                                                    <p class="points">Paste it into any post or page where you want the meeting or recording to appear.</p>
                                                </li>
                                            </ul>
                                            </p>
                                        </li>
                                    </ul>
                                    <hr>


                                    <!-- Markdown Code -->

                                    <!-- 

                                    <h2 class="svelte-prhgxu">FAQ</h2>
                                    <div class="accordion">
                                        <div class="accordion__item">
                                            <div class="accordion__header" data-toggle="#faq1">Installation Instructions</div>
                                            <div class="accordion__content" id="faq1">
                                                <dd class="svelte-14m4qi0" style="">
                                                    <h4>Generating Access Code</h4>
                                                    <ol>
                                                        <li>You need MeetHour Developers plan to access the WordPress MeetHour integration plugin</li>
                                                        <li>Go to <a href="https://portal.meethour.io/">MeetHour Dashboard</a></li>
                                                        <li>In Credentials Section you will find Client ID, Client Secret, API Key</li>
                                                        <li>Add those Credentials in Generate Access Token Page </li>
                                                        <li>Refresh the page once the access code is Generated </li>
                                                        <li>Now You Get to Access Different Plugin Features. Like</li>
                                                    </ol>
                                                    <ul>
                                                        <li>Scheduling a Meeting </li>
                                                        <li>Creating an Instant Meeting</li>
                                                        <li>Fetch all MeetHour Data to WordPress Database</li>
                                                        <li>Short Codes for Meetings and Recordings</li>
                                                    </ul>
                                                </dd>
                                            </div>
                                        </div>
                                        <div class="accordion__item">
                                            <div class="accordion__header" data-toggle="#faq3">How to Fetch MeetHour Users</div>
                                            <div class="accordion__content" id="faq3">
                                                <p>
                                                    <dd class="svelte-14m4qi0" style="">
                                                        <ul>
                                                            <li>Go to Users </li>
                                                            <li>Click on Fetch MeetHour Users </li>
                                                            <li>MeetHour Users Fetched Successfully</li>
                                                        </ul>
                                                    </dd>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="accordion__item">
                                            <div class="accordion__header" data-toggle="#faq4">How to Fetch MeetHour Meetings</div>
                                            <div class="accordion__content" id="faq4">
                                                <p>
                                                    <dd class="svelte-14m4qi0" style="">
                                                        <ul>
                                                            <li>Go to Meetings Section </li>
                                                            <li>Click on Fetch MeetHour Meetings </li>
                                                            <li>MeetHour Meetings Fetched Successfully</li>
                                                        </ul>
                                                    </dd>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="accordion__item">
                                            <div class="accordion__header" data-toggle="#faq5">How to Schedule Meetings</div>
                                            <div class="accordion__content" id="faq5">
                                                <p>
                                                    <dd class="svelte-14m4qi0" style="">
                                                        <p>Here you can Schedule Meeting for a Particular Time</p>
                                                        <ul>
                                                            <li>Go to Meetings Section </li>
                                                            <li>Click on Schedule Meetings </li>
                                                            <li>Fill The Form and Click Publish </li>
                                                            <li>Meeting Scheduled Successfully</li>
                                                        </ul>
                                                    </dd>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="accordion__item">
                                            <div class="accordion__header" data-toggle="#faq6">How to Instant Meetings</div>
                                            <div class="accordion__content" id="faq6">
                                                <p>
                                                    <dd class="svelte-14m4qi0" style="">
                                                        <p>This will Create Instant Meeting, Where Users can Instantly Join the Meeting.</p>
                                                        <ul>
                                                            <li>Go to Instant Meetings Section </li>
                                                            <li>Enter Meeting Name and Passcode </li>
                                                            <li>Get Meeting Link</li>
                                                        </ul>
                                                    </dd>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="accordion__item">
                                            <div class="accordion__header" data-toggle="#faq7">How to Fetch MeetHour Recordings</div>
                                            <div class="accordion__content" id="faq7">
                                                <p>
                                                    <dd class="svelte-14m4qi0" style="">
                                                        <p>This will Fetch all the MeetHour Recordings into WordPress Database</p>
                                                        <ul>
                                                            <li>Go to Recordings Menu </li>
                                                            <li>Click on Fetch MeetHour Recordings </li>
                                                            <li>Recordings Fetched Successfully</li>
                                                        </ul>
                                                    </dd>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="accordion__item">
                                            <div class="accordion__header" data-toggle="#faq8">How to Use ShortCodes</div>
                                            <div class="accordion__content" id="faq8">
                                                <p>
                                                    <dd class="svelte-14m4qi0" style="">
                                                        <ul>
                                                            <li>Copy the Meeting Short Code from Meetings Page and Paste it in the Post Editor for Meetings: <code>[meethour meeting_id=""]</code> </li>
                                                            <li>Copy the Recordings Short Code from Recordings Page and Paste in the Post Editor for Recordings: <code>[meethour recording_id=""]</code></li>
                                                        </ul>
                                                    </dd>
                                                </p>
                                            </div>
                                        </div>
                                    </div> -->
                                    <h3>Features</h3>
                                    <ul>
                                        <li>
                                            <p>Unlimited Meeting Duration in Free Plan<br>
                                                Enjoy endless meetings with no time restrictions in Meet Hour’s free plan, ensuring uninterrupted collaboration.</p>
                                        </li>
                                        <li>
                                            <p>Schedule a Meeting<br>
                                                Meeting organizer can invite participants in the meeting through email or by sharing the link with the participants via WhatsApp, Slack or Teams as well.</p>
                                        </li>
                                        <li>
                                            <p>Sync Meetings to Calendar<br>
                                                When the user schedules a meeting, it’s gets automatically attach to the Calendar of user account helping to organize &amp; manage meetings in one place.</p>
                                        </li>
                                        <li>
                                            <p>Meeting Prefix<br>
                                                The Meeting Prefix refers to a set of unique characters at the beginning of a meeting ID to uniquely promote your organizational initials.</p>
                                        </li>
                                        <li>
                                            <p>Branded Conference<br>
                                                Branded Conference is a unique feature of Meet Hour where a company or individual can have a branded conference of his own. You can attach your domain or use sub domain of Meet Hour for the conference call.</p>
                                        </li>
                                        <li>
                                            <p>Recordings<br>
                                                You can access all your recordings from the dashboard, you can play, download and share.</p>
                                        </li>
                                        <li>
                                            <p>Live Streaming<br>
                                                You can do live streaming from Meet Hour on a platform or multiple platforms. We provide parallel live streaming for up to 3 platforms. You can live stream on YouTube, Facebook, Instagram, LinkedIn, Twitch, Custom RTMP &amp; RTMPS and many more…</p>
                                        </li>
                                        <li>
                                            <p>Whiteboard<br>
                                                A whiteboard is a feature for the teams to collaborate and it is a learning space where both teacher and student can write and interact with.</p>
                                        </li>
                                        <li>
                                            <p>Screen Sharing<br>
                                                Screen sharing is a technology that allows one user to share their computer screen with others in real time.</p>
                                        </li>
                                        <li>
                                            <p>Join from Any Device<br>
                                                In Meet Hour the users can join from anywhere web browser, desktop app, mobile app android and ios</p>
                                        </li>
                                        <li>
                                            <p>Lobby Mode<br>
                                                Enabling Lobby mode makes the meeting more secure. It gives moderator the right to allow or reject the participants.</p>
                                        </li>
                                        <li>
                                            <p>End-to-End Encrypted<br>
                                                All the meetings in the Meet Hour are end-to-end encrypted. It provides a high level of security and privacy</p>
                                        </li>
                                        <li>
                                            <p>Chat with Participants<br>
                                                Meet Hour also has built-in chat provision where the participants can chat with each other, also the participants can send private messages to other participants</p>
                                        </li>
                                        <li>
                                            <p>Virtual Background<br>
                                                The virtual background feature in Meet Hour allows users to replace their actual background with a digitally generated image.</p>
                                        </li>
                                        <li>
                                            <p>Live pad<br>
                                                Live pad is a powerful collaborative editing tool that enables real-time document editing in Meet Hour. It facilitates seamless collaboration on the document remotely over the video conference call.</p>
                                        </li>
                                        <li>
                                            <p>Multiple Donate Option<br>
                                                Meet Hour has integrated Donor box, Click &amp; Pledge as the donation’s options within the conference. Fundraise via video call they can do it with the help of Meet Hour.</p>
                                        </li>
                                        <li>
                                            <p>Share YouTube Video<br>
                                                Meet Hour allows the users to share a YouTube video without sharing their screen</p>
                                        </li>
                                        <li>
                                            <p>Embed Meeting<br>
                                                Embed the meetings with just a line of code. This is available in the Developer plan and Enterprise plan</p>
                                        </li>
                                        <li>
                                            <p>Contacts<br>
                                                Access detailed information about any contact you add in the Meet Hour platform</p>
                                        </li>
                                        <li>
                                            <p>Meeting Analytics<br>
                                                Analyze your meeting data with our powerful reports built into the dashboard. Get detailed insights of meetings scheduled by you Get the data metrics like</p>
                                        </li>
                                        <li>
                                            <p>Webinar Mode<br>
                                                Webinar Mode offers experience for hosting large-scale events with audience participation. With built-in registration system attendees can sign up before the webinar, ensuring an organized attendee list.</p>
                                        </li>
                                        <li>
                                            <p>Voice Command<br>
                                                Use Meet Hour best in class voice command to perform specific actions within the meeting.</p>
                                        </li>
                                        <li>
                                            <p>Manage Video Quality<br>
                                                Manually manage the video quality from low definition to high definition.</p>
                                        </li>
                                        <li>
                                            <p>Speaker Stats<br>
                                                See the stats of the participants who have spoken for most of the time in the meeting. Check the stat live during the meeting.</p>
                                        </li>
                                        <li>
                                            <p>Keyboard Shortcuts<br>
                                                Users can perform specific meeting actions via shortcuts of the meeting.</p>
                                        </li>
                                        <li>
                                            <p>Raise Hand<br>
                                                A user can raise hand during the meeting if he/she wants to ask/say something while other participants are having a conversation.</p>
                                        </li>
                                        <li>
                                            <p>Picture In Picture Mode (Pip)<br>
                                                Meet Hour allows picture in picture mode when users are sharing the screen.</p>
                                        </li>
                                    </ul>
                                    <hr>

                                    <h3>Use Cases</h3>
                                    <ul>
                                        <li>
                                            <p>Video conferencing<br>
                                                Discover the power of video conferencing and learn what video conferencing is, explore its diverse applications across industries.</p>
                                        </li>
                                        <li>
                                            <p>Live Streaming<br>
                                                Meet Hour allows you to broadcast your conferences directly to popular channels like YouTube, LinkedIn, Instagram, and Facebook, all at once.</p>
                                        </li>
                                        <li>
                                            <p>Virtual Classrooms<br>
                                                Unlock a new dimension of education with Meet Hour. Whether you’re a school, university, corporate training center, or any organization.</p>
                                        </li>
                                        <li>
                                            <p>Virtual Events<br>
                                                In today’s digital age, virtual events have become an integral part of businesses, educational institutions, and organizations.</p>
                                        </li>
                                        <li>
                                            <p>Video KYC<br>
                                                Embrace the Future of Identity Verification with Video e-KYC. In a world driven by digital transformation, the need for seamless and secure verification.</p>
                                        </li>
                                        <li>
                                            <p>Webinars<br>
                                                Meet Hour Webinars offer a simple, secure, and reliable platform for your virtual gatherings. Let’s dive into the key features that make Meet Hour Webinars.</p>
                                        </li>
                                        <li>
                                            <p>Fundraising<br>
                                                At Meet Hour, we understand the importance of fundraising in today’s dynamic world. That’s why we’ve introduced seamless integration with – Donor box and Click pledge.</p>
                                        </li>
                                    </ul>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>
        </div>
        </article>
    </div>

    </div>
    <script>
        jQuery(function($) {
            $('#tabs .tab-content').hide();
            $('#tabs .tab-content:first').show();
            $('.nav-tab-wrapper a:first').addClass('nav-tab-active');

            $(".nav-tab-wrapper").on("click", ".nav-tab", function(e) {
                e.preventDefault();
                $(".nav-tab-wrapper a:first").removeClass("nav-tab-active");
                $(".tab-content").hide();
                $(this).addClass("nav-tab-active");
                $($(this).attr("href")).show();
            });
        })
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const togglers = document.querySelectorAll('[data-toggle]');

            togglers.forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    const selector = e.currentTarget.dataset.toggle
                    const block = document.querySelector(`${selector}`);
                    if (e.currentTarget.classList.contains('active')) {
                        block.style.maxHeight = '';
                    } else {
                        block.style.maxHeight = block.scrollHeight + 'px';
                    }

                    e.currentTarget.classList.toggle('active')
                })
            })
        })
    </script>
<?php
}
?>
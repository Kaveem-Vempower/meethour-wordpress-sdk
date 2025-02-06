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
                                    </div>
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
<?php
/*
This file maps the English translation keys into logical groups for the UI installation process, so that the interface can render them dynamically.
*/
return [

    'groups' => [
        'Side Navigation' => [
            'Test Form',
            'Page 1',
            'SEND Pupils',
            'Accommodations',
            'Majors',
            'Proficiencies',
            'Subjects',
            'Record Types',
            'Meeting Types',
            'Professionals',
            'User Groups',
            'Users',
            'MFA Settings',
            'MFA Setup',
            'System Backups',
        ],

        'Top Navigation' => [
            'Summary',
            'Medications',
            'Diagnoses',
            'Records',
            'Events',
            'Meetings',
            'Diet',
            'Family Members',
        ],

        'Authentication' => [
            'Login',
            'Logout',
            'Username',
            'Password',
            'Welcome,',
            'please login',
            'Username or password is incorrect.',
            'Please try again.',
        ],

        'Test Form' => [
            'Test',
        ],

        'Users' => [
            'Create User',
            'Mobile',
            'Position',
            'Added By',
            'Joined Date',
            'Expiry Date',
            'Group',
            'User Group',
            'Choose Group',
            'Edit User',
        ],

        'Backups' => [
            'Are you sure you want to create a new backup? This may take a moment.',
            'Create Backup',
            'File Name',
            'File Size',
            'Last Modified',
            'Download',
            'No backups found.',
        ],

        'MFA Settings' => [
            'Multi-Factor Authentication',
            'Choose how users must verify their identity when logging in.',
            'No MFA',
            'Users log in with username and password only. No additional verification step.',
            'Email Verification',
            'A one-time code is sent to the user\'s email address after login. Users must enter the code to continue.',
            'Authenticator App',
            'Users must set up an authenticator app and enter a time-based code each time they log in.',
        ],

        'MFA Setup' => [
            'MFA Setup',
            'Set up multi-factor authentication for your account.',
            'MFA is not required',
            'Your administrator has not enabled multi-factor authentication. No action is needed.',
            'Email Verification',
            'Your administrator requires email-based verification. A one-time code will be sent to your email each time you log in.',
            'Authenticator App',
            'Your administrator requires authenticator app verification. You will need to scan the QR code with your authenticator app.',
            'MFA is set up and active on your account.',
            'Verified',
        ],

        'Misc/General' => [
            'Actions',
            'First Name',
            'Last Name',
            'Save',
            'Update',
            'Save Changes',
        ],
    ],

];
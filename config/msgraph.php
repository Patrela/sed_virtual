<?php

return [

/*
 * The tenant ID for your Azure AD application
 */
'tenant_id' => env('MICROSOFT_GRAPH_TENANT_ID'),

/*
 * The client ID for your Azure AD application
 */
'client_id' => env('MICROSOFT_GRAPH_CLIENT_ID'),

/*
 * The client secret for your Azure AD application
 */
'client_secret' => env('MICROSOFT_GRAPH_CLIENT_SECRET'),

/*
 * The username of the account that will be used to send emails
 */
'username' => env('MICROSOFT_GRAPH_USERNAME'),

/*
 * The password of the account that will be used to send emails
 */
'password' => env('MICROSOFT_GRAPH_PASSWORD'),

/*
 * The endpoint to acquire the token
 */
'token_url' => 'https://login.microsoftonline.com/{tenant_id}/oauth2/v2.0/token',

/*
 * The endpoint to send the emails
 */
'mail_url' => 'https://graph.microsoft.com/v1.0/users/{username}/sendMail',

];

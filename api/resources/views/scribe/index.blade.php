<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>SDP Cinema API Documentation</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://localhost";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.11.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.11.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-auth" class="tocify-header">
                <li class="tocify-item level-1" data-unique="auth">
                    <a href="#auth">Auth</a>
                </li>
                                    <ul id="tocify-subheader-auth" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="auth-POSTapi-auth-register">
                                <a href="#auth-POSTapi-auth-register">Register.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="auth-POSTapi-auth-login">
                                <a href="#auth-POSTapi-auth-login">Log in.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="auth-POSTapi-auth-logout">
                                <a href="#auth-POSTapi-auth-logout">Log out.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="auth-GETapi-auth-me">
                                <a href="#auth-GETapi-auth-me">The authenticated user.</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-bookings" class="tocify-header">
                <li class="tocify-item level-1" data-unique="bookings">
                    <a href="#bookings">Bookings</a>
                </li>
                                    <ul id="tocify-subheader-bookings" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="bookings-POSTapi-bookings">
                                <a href="#bookings-POSTapi-bookings">Confirm a booking (atomic).</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-cinemas" class="tocify-header">
                <li class="tocify-item level-1" data-unique="cinemas">
                    <a href="#cinemas">Cinemas</a>
                </li>
                                    <ul id="tocify-subheader-cinemas" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="cinemas-GETapi-cinemas">
                                <a href="#cinemas-GETapi-cinemas">List cinemas.</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-food" class="tocify-header">
                <li class="tocify-item level-1" data-unique="food">
                    <a href="#food">Food</a>
                </li>
                                    <ul id="tocify-subheader-food" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="food-GETapi-food-items">
                                <a href="#food-GETapi-food-items">List food & beverage items.</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-movies" class="tocify-header">
                <li class="tocify-item level-1" data-unique="movies">
                    <a href="#movies">Movies</a>
                </li>
                                    <ul id="tocify-subheader-movies" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="movies-GETapi-movies">
                                <a href="#movies-GETapi-movies">List movies.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="movies-GETapi-movies--id-">
                                <a href="#movies-GETapi-movies--id-">Get a movie.</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-seat-locks" class="tocify-header">
                <li class="tocify-item level-1" data-unique="seat-locks">
                    <a href="#seat-locks">Seat locks</a>
                </li>
                                    <ul id="tocify-subheader-seat-locks" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="seat-locks-POSTapi-showtimes--showtime_id--seats--seatCode--lock">
                                <a href="#seat-locks-POSTapi-showtimes--showtime_id--seats--seatCode--lock">Acquire a seat hold.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="seat-locks-DELETEapi-showtimes--showtime_id--seats--seatCode--lock">
                                <a href="#seat-locks-DELETEapi-showtimes--showtime_id--seats--seatCode--lock">Release a seat hold owned by the caller.</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-showtimes" class="tocify-header">
                <li class="tocify-item level-1" data-unique="showtimes">
                    <a href="#showtimes">Showtimes</a>
                </li>
                                    <ul id="tocify-subheader-showtimes" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="showtimes-GETapi-showtimes">
                                <a href="#showtimes-GETapi-showtimes">List showtimes.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="showtimes-GETapi-showtimes--showtime_id--seats">
                                <a href="#showtimes-GETapi-showtimes--showtime_id--seats">Get the seat map for a showtime.</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: June 29, 2026</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<aside>
    <strong>Base URL</strong>: <code>http://localhost</code>
</aside>
<pre><code>This documentation aims to provide all the information you need to work with our API.

&lt;aside&gt;As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).&lt;/aside&gt;</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>To authenticate requests, include an <strong><code>Authorization</code></strong> header with the value <strong><code>"Bearer {YOUR_AUTH_KEY}"</code></strong>.</p>
<p>All authenticated endpoints are marked with a <code>requires authentication</code> badge in the documentation below.</p>
<p>Obtain a bearer token from <code>POST /api/auth/register</code> or <code>POST /api/auth/login</code>, then send it as <code>Authorization: Bearer {token}</code>.</p>

        <h1 id="auth">Auth</h1>

    

                                <h2 id="auth-POSTapi-auth-register">Register.</h2>

<p>
</p>

<p>Create an account and return a Sanctum bearer token. Send the token as
<code>Authorization: Bearer {token}</code> on every mutating request.</p>

<span id="example-requests-POSTapi-auth-register">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/auth/register" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"Aisyah\",
    \"email\": \"aisyah@example.com\",
    \"password\": \"password123\",
    \"password_confirmation\": \"password123\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/auth/register"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "Aisyah",
    "email": "aisyah@example.com",
    "password": "password123",
    "password_confirmation": "password123"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-auth-register">
            <blockquote>
            <p>Example response (201):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;user&quot;: {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;Aisyah&quot;,
            &quot;email&quot;: &quot;aisyah@example.com&quot;
        },
        &quot;token&quot;: &quot;1|abcDEF...&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;The email has already been taken.&quot;,
    &quot;errors&quot;: {
        &quot;email&quot;: [
            &quot;The email has already been taken.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-auth-register" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-auth-register"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-auth-register"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-auth-register" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-auth-register">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-auth-register" data-method="POST"
      data-path="api/auth/register"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-auth-register', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-auth-register"
                    onclick="tryItOut('POSTapi-auth-register');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-auth-register"
                    onclick="cancelTryOut('POSTapi-auth-register');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-auth-register"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/auth/register</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-auth-register"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-auth-register"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-auth-register"
               value="Aisyah"
               data-component="body">
    <br>
<p>The user's name. Example: <code>Aisyah</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-auth-register"
               value="aisyah@example.com"
               data-component="body">
    <br>
<p>A unique email. Example: <code>aisyah@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-auth-register"
               value="password123"
               data-component="body">
    <br>
<p>Min 8 chars. Example: <code>password123</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password_confirmation</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password_confirmation"                data-endpoint="POSTapi-auth-register"
               value="password123"
               data-component="body">
    <br>
<p>Must match password. Example: <code>password123</code></p>
        </div>
        </form>

                    <h2 id="auth-POSTapi-auth-login">Log in.</h2>

<p>
</p>

<p>Verify credentials and return a fresh Sanctum bearer token.</p>

<span id="example-requests-POSTapi-auth-login">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/auth/login" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"email\": \"aisyah@example.com\",
    \"password\": \"password123\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/auth/login"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "email": "aisyah@example.com",
    "password": "password123"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-auth-login">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;user&quot;: {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;Aisyah&quot;,
            &quot;email&quot;: &quot;aisyah@example.com&quot;
        },
        &quot;token&quot;: &quot;2|abcDEF...&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;The provided credentials are incorrect.&quot;,
    &quot;errors&quot;: {
        &quot;email&quot;: [
            &quot;The provided credentials are incorrect.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-auth-login" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-auth-login"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-auth-login"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-auth-login" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-auth-login">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-auth-login" data-method="POST"
      data-path="api/auth/login"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-auth-login', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-auth-login"
                    onclick="tryItOut('POSTapi-auth-login');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-auth-login"
                    onclick="cancelTryOut('POSTapi-auth-login');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-auth-login"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/auth/login</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-auth-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-auth-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-auth-login"
               value="aisyah@example.com"
               data-component="body">
    <br>
<p>Example: <code>aisyah@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-auth-login"
               value="password123"
               data-component="body">
    <br>
<p>Example: <code>password123</code></p>
        </div>
        </form>

                    <h2 id="auth-POSTapi-auth-logout">Log out.</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Revoke the bearer token used on this request. Other tokens stay valid.</p>

<span id="example-requests-POSTapi-auth-logout">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/auth/logout" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/auth/logout"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-auth-logout">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;message&quot;: &quot;Logged out.&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-auth-logout" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-auth-logout"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-auth-logout"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-auth-logout" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-auth-logout">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-auth-logout" data-method="POST"
      data-path="api/auth/logout"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-auth-logout', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-auth-logout"
                    onclick="tryItOut('POSTapi-auth-logout');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-auth-logout"
                    onclick="cancelTryOut('POSTapi-auth-logout');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-auth-logout"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/auth/logout</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-auth-logout"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-auth-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-auth-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="auth-GETapi-auth-me">The authenticated user.</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Return the account bound to the bearer token. Useful to rehydrate session
state on app launch.</p>

<span id="example-requests-GETapi-auth-me">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/auth/me" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/auth/me"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-auth-me">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;Aisyah&quot;,
        &quot;email&quot;: &quot;aisyah@example.com&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-auth-me" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-auth-me"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-auth-me"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-auth-me" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-auth-me">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-auth-me" data-method="GET"
      data-path="api/auth/me"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-auth-me', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-auth-me"
                    onclick="tryItOut('GETapi-auth-me');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-auth-me"
                    onclick="cancelTryOut('GETapi-auth-me');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-auth-me"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/auth/me</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-auth-me"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-auth-me"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-auth-me"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="bookings">Bookings</h1>

    

                                <h2 id="bookings-POSTapi-bookings">Confirm a booking (atomic).</h2>

<p>
</p>

<p>Validates every seat is still held by the caller, inserts booking_seats +
booking_food_items, deletes the holds, attaches a stub payment, and
computes totals server-side (integer minor units, RM) — all in one DB
transaction. Broadcasts <code>booked</code> per seat on the showtime channel.</p>

<span id="example-requests-POSTapi-bookings">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/bookings" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"showtime_id\": 16,
    \"seat_codes\": [
        \"architecto\"
    ],
    \"promo_code\": \"n\",
    \"payment_method\": \"crypto\",
    \"food\": [
        {
            \"food_item_id\": 16,
            \"qty\": 22
        }
    ]
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/bookings"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "showtime_id": 16,
    "seat_codes": [
        "architecto"
    ],
    "promo_code": "n",
    "payment_method": "crypto",
    "food": [
        {
            "food_item_id": 16,
            "qty": 22
        }
    ]
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-bookings">
            <blockquote>
            <p>Example response (201, confirmed):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 501,
        &quot;reference&quot;: &quot;SDP-2026-000501&quot;,
        &quot;status&quot;: &quot;confirmed&quot;,
        &quot;total&quot;: 8565
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (409):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;One or more seats are no longer available.&quot;,
    &quot;errors&quot;: {
        &quot;seat_codes&quot;: [
            &quot;D5 is no longer held by you.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-bookings" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-bookings"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-bookings"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-bookings" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-bookings">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-bookings" data-method="POST"
      data-path="api/bookings"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-bookings', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-bookings"
                    onclick="tryItOut('POSTapi-bookings');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-bookings"
                    onclick="cancelTryOut('POSTapi-bookings');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-bookings"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/bookings</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-bookings"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-bookings"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>showtime_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="showtime_id"                data-endpoint="POSTapi-bookings"
               value="16"
               data-component="body">
    <br>
<p>Must match an existing stored value. Example: <code>16</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>seat_codes</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="seat_codes[0]"                data-endpoint="POSTapi-bookings"
               data-component="body">
        <input type="text" style="display: none"
               name="seat_codes[1]"                data-endpoint="POSTapi-bookings"
               data-component="body">
    <br>

        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>food</code></b>&nbsp;&nbsp;
<small>object[]</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
<br>

            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>food_item_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="food.0.food_item_id"                data-endpoint="POSTapi-bookings"
               value="16"
               data-component="body">
    <br>
<p>This field is required when <code>food</code> is present. Must match an existing stored value. Example: <code>16</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>qty</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="food.0.qty"                data-endpoint="POSTapi-bookings"
               value="22"
               data-component="body">
    <br>
<p>This field is required when <code>food</code> is present. Must be at least 1. Must not be greater than 99. Example: <code>22</code></p>
                    </div>
                                    </details>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>promo_code</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="promo_code"                data-endpoint="POSTapi-bookings"
               value="n"
               data-component="body">
    <br>
<p>Must not be greater than 50 characters. Example: <code>n</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>payment_method</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="payment_method"                data-endpoint="POSTapi-bookings"
               value="crypto"
               data-component="body">
    <br>
<p>Example: <code>crypto</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>card</code></li> <li><code>bank</code></li> <li><code>crypto</code></li></ul>
        </div>
        </form>

                <h1 id="cinemas">Cinemas</h1>

    

                                <h2 id="cinemas-GETapi-cinemas">List cinemas.</h2>

<p>
</p>

<p>Returns cinemas with their halls.</p>

<span id="example-requests-GETapi-cinemas">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/cinemas" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/cinemas"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-cinemas">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;TGV Suria KLCC&quot;,
            &quot;city&quot;: &quot;Kuala Lumpur&quot;,
            &quot;address&quot;: &quot;Level 3, Suria KLCC, Kuala Lumpur City Centre, 50088 Kuala Lumpur&quot;,
            &quot;halls&quot;: [
                {
                    &quot;id&quot;: 1,
                    &quot;cinema_id&quot;: 1,
                    &quot;name&quot;: &quot;Hall 1&quot;
                }
            ]
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-cinemas" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-cinemas"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-cinemas"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-cinemas" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-cinemas">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-cinemas" data-method="GET"
      data-path="api/cinemas"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-cinemas', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-cinemas"
                    onclick="tryItOut('GETapi-cinemas');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-cinemas"
                    onclick="cancelTryOut('GETapi-cinemas');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-cinemas"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/cinemas</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-cinemas"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-cinemas"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="food">Food</h1>

    

                                <h2 id="food-GETapi-food-items">List food &amp; beverage items.</h2>

<p>
</p>

<p>Returns the F&amp;B catalog grouped-friendly by category.</p>

<span id="example-requests-GETapi-food-items">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/food-items" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/food-items"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-food-items">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 2,
            &quot;category&quot;: &quot;combo&quot;,
            &quot;name&quot;: &quot;Family Feast Combo&quot;,
            &quot;description&quot;: &quot;1 large popcorn + 4 regular drinks + 2 nacho boxes&quot;,
            &quot;price&quot;: 6500,
            &quot;discount_price&quot;: 5500,
            &quot;currency&quot;: &quot;RM&quot;,
            &quot;image_url&quot;: &quot;https://images.unsplash.com/photo-1578849278619-e73505e9610f?w=400&quot;
        },
        {
            &quot;id&quot;: 3,
            &quot;category&quot;: &quot;combo&quot;,
            &quot;name&quot;: &quot;Solo Saver Combo&quot;,
            &quot;description&quot;: &quot;1 medium popcorn + 1 regular drink&quot;,
            &quot;price&quot;: 2200,
            &quot;discount_price&quot;: null,
            &quot;currency&quot;: &quot;RM&quot;,
            &quot;image_url&quot;: &quot;https://images.unsplash.com/photo-1505686994434-e3cc5abf1330?w=400&quot;
        },
        {
            &quot;id&quot;: 1,
            &quot;category&quot;: &quot;combo&quot;,
            &quot;name&quot;: &quot;Sweet Couple Combo&quot;,
            &quot;description&quot;: &quot;2 medium sweet popcorn + 2 regular soft drinks&quot;,
            &quot;price&quot;: 3900,
            &quot;discount_price&quot;: 3200,
            &quot;currency&quot;: &quot;RM&quot;,
            &quot;image_url&quot;: &quot;https://images.unsplash.com/photo-1585647347483-22b66260dfff?w=400&quot;
        },
        {
            &quot;id&quot;: 4,
            &quot;category&quot;: &quot;food_snacks&quot;,
            &quot;name&quot;: &quot;Caramel Popcorn (Large)&quot;,
            &quot;description&quot;: &quot;Freshly popped caramel-coated popcorn&quot;,
            &quot;price&quot;: 1800,
            &quot;discount_price&quot;: null,
            &quot;currency&quot;: &quot;RM&quot;,
            &quot;image_url&quot;: &quot;https://images.unsplash.com/photo-1578849278619-e73505e9610f?w=400&quot;
        },
        {
            &quot;id&quot;: 6,
            &quot;category&quot;: &quot;food_snacks&quot;,
            &quot;name&quot;: &quot;Hot Dog&quot;,
            &quot;description&quot;: &quot;Classic beef hot dog with toppings&quot;,
            &quot;price&quot;: 1300,
            &quot;discount_price&quot;: null,
            &quot;currency&quot;: &quot;RM&quot;,
            &quot;image_url&quot;: &quot;https://images.unsplash.com/photo-1612392062798-2dd6e0b6b86b?w=400&quot;
        },
        {
            &quot;id&quot;: 5,
            &quot;category&quot;: &quot;food_snacks&quot;,
            &quot;name&quot;: &quot;Loaded Nachos&quot;,
            &quot;description&quot;: &quot;Tortilla chips with cheese sauce and jalape&ntilde;os&quot;,
            &quot;price&quot;: 1500,
            &quot;discount_price&quot;: 1200,
            &quot;currency&quot;: &quot;RM&quot;,
            &quot;image_url&quot;: &quot;https://images.unsplash.com/photo-1513456852971-30c0b8199d4d?w=400&quot;
        },
        {
            &quot;id&quot;: 8,
            &quot;category&quot;: &quot;beverages&quot;,
            &quot;name&quot;: &quot;Large Sprite&quot;,
            &quot;description&quot;: &quot;Chilled Sprite, large size&quot;,
            &quot;price&quot;: 1050,
            &quot;discount_price&quot;: null,
            &quot;currency&quot;: &quot;RM&quot;,
            &quot;image_url&quot;: &quot;https://images.unsplash.com/photo-1625772299848-391b6a87d7b3?w=400&quot;
        },
        {
            &quot;id&quot;: 9,
            &quot;category&quot;: &quot;beverages&quot;,
            &quot;name&quot;: &quot;Mineral Water&quot;,
            &quot;description&quot;: &quot;500ml bottled mineral water&quot;,
            &quot;price&quot;: 500,
            &quot;discount_price&quot;: null,
            &quot;currency&quot;: &quot;RM&quot;,
            &quot;image_url&quot;: &quot;https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=400&quot;
        },
        {
            &quot;id&quot;: 7,
            &quot;category&quot;: &quot;beverages&quot;,
            &quot;name&quot;: &quot;Regular Coke&quot;,
            &quot;description&quot;: &quot;Chilled Coca-Cola, regular size&quot;,
            &quot;price&quot;: 850,
            &quot;discount_price&quot;: null,
            &quot;currency&quot;: &quot;RM&quot;,
            &quot;image_url&quot;: &quot;https://images.unsplash.com/photo-1554866585-cd94860890b7?w=400&quot;
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-food-items" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-food-items"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-food-items"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-food-items" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-food-items">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-food-items" data-method="GET"
      data-path="api/food-items"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-food-items', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-food-items"
                    onclick="tryItOut('GETapi-food-items');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-food-items"
                    onclick="cancelTryOut('GETapi-food-items');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-food-items"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/food-items</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-food-items"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-food-items"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="movies">Movies</h1>

    

                                <h2 id="movies-GETapi-movies">List movies.</h2>

<p>
</p>

<p>Returns the movie catalog for the Home screen.</p>

<span id="example-requests-GETapi-movies">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/movies" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/movies"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-movies">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 3,
            &quot;title&quot;: &quot;Inside Out 2&quot;,
            &quot;synopsis&quot;: &quot;Riley enters her teenage years and Headquarters undergoes a sudden demolition to make room for something new: brand-new Emotions. Joy, Sadness, Anger, Fear and Disgust must make space for Anxiety and friends.&quot;,
            &quot;duration_min&quot;: 96,
            &quot;release_date&quot;: &quot;2026-06-19&quot;,
            &quot;age_rating&quot;: &quot;U&quot;,
            &quot;imdb_rating&quot;: 7.6,
            &quot;poster_url&quot;: &quot;https://image.tmdb.org/t/p/w500/vpnVM9B6NMmQpWeZvzLvDESb2QY.jpg&quot;,
            &quot;trailer_url&quot;: &quot;https://www.youtube.com/watch?v=LEjhY15eCx0&quot;,
            &quot;genres&quot;: [
                &quot;Animation&quot;,
                &quot;Family&quot;,
                &quot;Comedy&quot;
            ],
            &quot;casts&quot;: [
                &quot;Amy Poehler&quot;,
                &quot;Maya Hawke&quot;,
                &quot;Kensington Tallman&quot;,
                &quot;Phyllis Smith&quot;
            ],
            &quot;director&quot;: &quot;Kelsey Mann&quot;,
            &quot;writers&quot;: [
                &quot;Meg LeFauve&quot;,
                &quot;Dave Holstein&quot;
            ],
            &quot;sections&quot;: [
                &quot;new_releases&quot;,
                &quot;recommended&quot;
            ]
        },
        {
            &quot;id&quot;: 1,
            &quot;title&quot;: &quot;Venom: The Last Dance&quot;,
            &quot;synopsis&quot;: &quot;Eddie Brock and Venom are on the run. Hunted by both of their worlds and with the net closing in, the duo are forced into a devastating decision that will bring the curtain down on their symbiotic relationship.&quot;,
            &quot;duration_min&quot;: 109,
            &quot;release_date&quot;: &quot;2026-06-12&quot;,
            &quot;age_rating&quot;: &quot;P13&quot;,
            &quot;imdb_rating&quot;: 7.1,
            &quot;poster_url&quot;: &quot;https://image.tmdb.org/t/p/w500/aosm8NMQ3UyoBVpSxyimorCQykC.jpg&quot;,
            &quot;trailer_url&quot;: &quot;https://www.youtube.com/watch?v=__2bjWbetsB8&quot;,
            &quot;genres&quot;: [
                &quot;Action&quot;,
                &quot;Sci-Fi&quot;,
                &quot;Adventure&quot;
            ],
            &quot;casts&quot;: [
                &quot;Tom Hardy&quot;,
                &quot;Chiwetel Ejiofor&quot;,
                &quot;Juno Temple&quot;,
                &quot;Rhys Ifans&quot;
            ],
            &quot;director&quot;: &quot;Kelly Marcel&quot;,
            &quot;writers&quot;: [
                &quot;Kelly Marcel&quot;,
                &quot;Tom Hardy&quot;
            ],
            &quot;sections&quot;: [
                &quot;new_releases&quot;,
                &quot;popular&quot;
            ]
        },
        {
            &quot;id&quot;: 4,
            &quot;title&quot;: &quot;The Batman: Shadows&quot;,
            &quot;synopsis&quot;: &quot;A new threat rises over Gotham as the Dark Knight hunts a killer leaving cryptic clues across the city.&quot;,
            &quot;duration_min&quot;: 142,
            &quot;release_date&quot;: &quot;2026-06-05&quot;,
            &quot;age_rating&quot;: &quot;18&quot;,
            &quot;imdb_rating&quot;: 7.9,
            &quot;poster_url&quot;: &quot;https://image.tmdb.org/t/p/w500/74xTEgt7R36Fpooo50r9T25onhq.jpg&quot;,
            &quot;trailer_url&quot;: &quot;https://www.youtube.com/watch?v=dQw4w9WgXcQ&quot;,
            &quot;genres&quot;: [
                &quot;Action&quot;,
                &quot;Crime&quot;,
                &quot;Thriller&quot;
            ],
            &quot;casts&quot;: [
                &quot;Robert Pattinson&quot;,
                &quot;Zo&euml; Kravitz&quot;,
                &quot;Colin Farrell&quot;
            ],
            &quot;director&quot;: &quot;Matt Reeves&quot;,
            &quot;writers&quot;: [
                &quot;Matt Reeves&quot;,
                &quot;Peter Craig&quot;
            ],
            &quot;sections&quot;: [
                &quot;popular&quot;
            ]
        },
        {
            &quot;id&quot;: 2,
            &quot;title&quot;: &quot;Dune: Part Two&quot;,
            &quot;synopsis&quot;: &quot;Paul Atreides unites with the Fremen while seeking revenge against the conspirators who destroyed his family. Facing a choice between the love of his life and the fate of the universe, he endeavors to prevent a terrible future.&quot;,
            &quot;duration_min&quot;: 166,
            &quot;release_date&quot;: &quot;2026-05-28&quot;,
            &quot;age_rating&quot;: &quot;P13&quot;,
            &quot;imdb_rating&quot;: 8.5,
            &quot;poster_url&quot;: &quot;https://image.tmdb.org/t/p/w500/1pdfLvkbY9ohJlCjQH2CZjjYVvJ.jpg&quot;,
            &quot;trailer_url&quot;: &quot;https://www.youtube.com/watch?v=Way9Dexny3w&quot;,
            &quot;genres&quot;: [
                &quot;Sci-Fi&quot;,
                &quot;Drama&quot;,
                &quot;Adventure&quot;
            ],
            &quot;casts&quot;: [
                &quot;Timoth&eacute;e Chalamet&quot;,
                &quot;Zendaya&quot;,
                &quot;Rebecca Ferguson&quot;,
                &quot;Javier Bardem&quot;
            ],
            &quot;director&quot;: &quot;Denis Villeneuve&quot;,
            &quot;writers&quot;: [
                &quot;Denis Villeneuve&quot;,
                &quot;Jon Spaihts&quot;
            ],
            &quot;sections&quot;: [
                &quot;popular&quot;,
                &quot;recommended&quot;
            ]
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-movies" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-movies"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-movies"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-movies" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-movies">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-movies" data-method="GET"
      data-path="api/movies"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-movies', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-movies"
                    onclick="tryItOut('GETapi-movies');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-movies"
                    onclick="cancelTryOut('GETapi-movies');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-movies"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/movies</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-movies"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-movies"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="movies-GETapi-movies--id-">Get a movie.</h2>

<p>
</p>

<p>Returns one movie with its casts and reviews (newest first) plus review aggregates.</p>

<span id="example-requests-GETapi-movies--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/movies/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/movies/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-movies--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;title&quot;: &quot;Venom: The Last Dance&quot;,
        &quot;synopsis&quot;: &quot;Eddie Brock and Venom are on the run. Hunted by both of their worlds and with the net closing in, the duo are forced into a devastating decision that will bring the curtain down on their symbiotic relationship.&quot;,
        &quot;duration_min&quot;: 109,
        &quot;release_date&quot;: &quot;2026-06-12&quot;,
        &quot;age_rating&quot;: &quot;P13&quot;,
        &quot;imdb_rating&quot;: 7.1,
        &quot;poster_url&quot;: &quot;https://image.tmdb.org/t/p/w500/aosm8NMQ3UyoBVpSxyimorCQykC.jpg&quot;,
        &quot;trailer_url&quot;: &quot;https://www.youtube.com/watch?v=__2bjWbetsB8&quot;,
        &quot;genres&quot;: [
            &quot;Action&quot;,
            &quot;Sci-Fi&quot;,
            &quot;Adventure&quot;
        ],
        &quot;casts&quot;: [
            &quot;Tom Hardy&quot;,
            &quot;Chiwetel Ejiofor&quot;,
            &quot;Juno Temple&quot;,
            &quot;Rhys Ifans&quot;
        ],
        &quot;director&quot;: &quot;Kelly Marcel&quot;,
        &quot;writers&quot;: [
            &quot;Kelly Marcel&quot;,
            &quot;Tom Hardy&quot;
        ],
        &quot;sections&quot;: [
            &quot;new_releases&quot;,
            &quot;popular&quot;
        ],
        &quot;reviews_count&quot;: 3,
        &quot;reviews_avg&quot;: 4,
        &quot;reviews&quot;: [
            {
                &quot;id&quot;: 3,
                &quot;rating&quot;: 3,
                &quot;title&quot;: &quot;Okay-lah&quot;,
                &quot;body&quot;: &quot;Enjoyable but predictable. The CGI was great though.&quot;,
                &quot;author&quot;: &quot;Aina Yusof&quot;,
                &quot;created_at&quot;: &quot;2026-06-22T19:12:00+00:00&quot;
            },
            {
                &quot;id&quot;: 2,
                &quot;rating&quot;: 4,
                &quot;title&quot;: &quot;Fun ride&quot;,
                &quot;body&quot;: &quot;A bit messy in the middle but a satisfying send-off. Stay for the credits.&quot;,
                &quot;author&quot;: &quot;Daniel Lim&quot;,
                &quot;created_at&quot;: &quot;2026-06-21T14:30:00+00:00&quot;
            },
            {
                &quot;id&quot;: 1,
                &quot;rating&quot;: 5,
                &quot;title&quot;: &quot;Best of the trilogy&quot;,
                &quot;body&quot;: &quot;The chemistry between Eddie and Venom carries the whole film. Action set pieces were wild.&quot;,
                &quot;author&quot;: &quot;Sofia Rahman&quot;,
                &quot;created_at&quot;: &quot;2026-06-20T10:00:00+00:00&quot;
            }
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-movies--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-movies--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-movies--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-movies--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-movies--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-movies--id-" data-method="GET"
      data-path="api/movies/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-movies--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-movies--id-"
                    onclick="tryItOut('GETapi-movies--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-movies--id-"
                    onclick="cancelTryOut('GETapi-movies--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-movies--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/movies/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-movies--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-movies--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-movies--id-"
               value="1"
               data-component="url">
    <br>
<p>The movie id. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="seat-locks">Seat locks</h1>

    

                                <h2 id="seat-locks-POSTapi-showtimes--showtime_id--seats--seatCode--lock">Acquire a seat hold.</h2>

<p>
</p>

<p>Atomic FCFS hold guarded by UNIQUE(showtime_id, seat_id): first writer
wins, the loser gets 409. TTL ~5 min. Broadcasts <code>held</code> on the showtime
channel.</p>

<span id="example-requests-POSTapi-showtimes--showtime_id--seats--seatCode--lock">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/showtimes/1/seats/D4/lock" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/showtimes/1/seats/D4/lock"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-showtimes--showtime_id--seats--seatCode--lock">
            <blockquote>
            <p>Example response (201):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;showtime_id&quot;: 1,
        &quot;seat_code&quot;: &quot;D4&quot;,
        &quot;status&quot;: &quot;held&quot;,
        &quot;holder&quot;: &quot;you&quot;,
        &quot;expires_at&quot;: &quot;2026-07-02T19:05:00+08:00&quot;,
        &quot;ttl_seconds&quot;: 300
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (409):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Seat is no longer available.&quot;,
    &quot;errors&quot;: {
        &quot;seat&quot;: [
            &quot;This seat is already held or booked.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-showtimes--showtime_id--seats--seatCode--lock" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-showtimes--showtime_id--seats--seatCode--lock"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-showtimes--showtime_id--seats--seatCode--lock"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-showtimes--showtime_id--seats--seatCode--lock" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-showtimes--showtime_id--seats--seatCode--lock">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-showtimes--showtime_id--seats--seatCode--lock" data-method="POST"
      data-path="api/showtimes/{showtime_id}/seats/{seatCode}/lock"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-showtimes--showtime_id--seats--seatCode--lock', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-showtimes--showtime_id--seats--seatCode--lock"
                    onclick="tryItOut('POSTapi-showtimes--showtime_id--seats--seatCode--lock');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-showtimes--showtime_id--seats--seatCode--lock"
                    onclick="cancelTryOut('POSTapi-showtimes--showtime_id--seats--seatCode--lock');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-showtimes--showtime_id--seats--seatCode--lock"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/showtimes/{showtime_id}/seats/{seatCode}/lock</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-showtimes--showtime_id--seats--seatCode--lock"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-showtimes--showtime_id--seats--seatCode--lock"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>showtime_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="showtime_id"                data-endpoint="POSTapi-showtimes--showtime_id--seats--seatCode--lock"
               value="1"
               data-component="url">
    <br>
<p>The ID of the showtime. Example: <code>1</code></p>
            </div>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>seatCode</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="seatCode"                data-endpoint="POSTapi-showtimes--showtime_id--seats--seatCode--lock"
               value="D4"
               data-component="url">
    <br>
<p>The seat code. Example: <code>D4</code></p>
            </div>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>showtime</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="showtime"                data-endpoint="POSTapi-showtimes--showtime_id--seats--seatCode--lock"
               value="1"
               data-component="url">
    <br>
<p>The showtime id. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="seat-locks-DELETEapi-showtimes--showtime_id--seats--seatCode--lock">Release a seat hold owned by the caller.</h2>

<p>
</p>

<p>Only the holder may release; otherwise the hold expires on its TTL.
Broadcasts <code>available</code> on the showtime channel.</p>

<span id="example-requests-DELETEapi-showtimes--showtime_id--seats--seatCode--lock">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost/api/showtimes/1/seats/D4/lock" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/showtimes/1/seats/D4/lock"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-showtimes--showtime_id--seats--seatCode--lock">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;showtime_id&quot;: 1,
        &quot;seat_code&quot;: &quot;D4&quot;,
        &quot;status&quot;: &quot;available&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (403):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;You do not hold this seat.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-showtimes--showtime_id--seats--seatCode--lock" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-showtimes--showtime_id--seats--seatCode--lock"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-showtimes--showtime_id--seats--seatCode--lock"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-showtimes--showtime_id--seats--seatCode--lock" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-showtimes--showtime_id--seats--seatCode--lock">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-showtimes--showtime_id--seats--seatCode--lock" data-method="DELETE"
      data-path="api/showtimes/{showtime_id}/seats/{seatCode}/lock"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-showtimes--showtime_id--seats--seatCode--lock', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-showtimes--showtime_id--seats--seatCode--lock"
                    onclick="tryItOut('DELETEapi-showtimes--showtime_id--seats--seatCode--lock');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-showtimes--showtime_id--seats--seatCode--lock"
                    onclick="cancelTryOut('DELETEapi-showtimes--showtime_id--seats--seatCode--lock');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-showtimes--showtime_id--seats--seatCode--lock"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/showtimes/{showtime_id}/seats/{seatCode}/lock</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-showtimes--showtime_id--seats--seatCode--lock"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-showtimes--showtime_id--seats--seatCode--lock"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>showtime_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="showtime_id"                data-endpoint="DELETEapi-showtimes--showtime_id--seats--seatCode--lock"
               value="1"
               data-component="url">
    <br>
<p>The ID of the showtime. Example: <code>1</code></p>
            </div>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>seatCode</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="seatCode"                data-endpoint="DELETEapi-showtimes--showtime_id--seats--seatCode--lock"
               value="D4"
               data-component="url">
    <br>
<p>The seat code. Example: <code>D4</code></p>
            </div>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>showtime</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="showtime"                data-endpoint="DELETEapi-showtimes--showtime_id--seats--seatCode--lock"
               value="1"
               data-component="url">
    <br>
<p>The showtime id. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="showtimes">Showtimes</h1>

    

                                <h2 id="showtimes-GETapi-showtimes">List showtimes.</h2>

<p>
</p>

<p>Returns screenings, optionally filtered by movie, cinema, hall, tier or date.</p>

<span id="example-requests-GETapi-showtimes">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/showtimes?movie_id=1&amp;cinema_id=1&amp;hall_id=1&amp;tier_id=1&amp;date=2026-06-29" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"movie_id\": 16,
    \"cinema_id\": 16,
    \"hall_id\": 16,
    \"tier_id\": 16,
    \"date\": \"2026-06-29\",
    \"from\": \"2026-06-29T15:54:14\",
    \"to\": \"2026-06-29T15:54:14\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/showtimes"
);

const params = {
    "movie_id": "1",
    "cinema_id": "1",
    "hall_id": "1",
    "tier_id": "1",
    "date": "2026-06-29",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "movie_id": 16,
    "cinema_id": 16,
    "hall_id": 16,
    "tier_id": 16,
    "date": "2026-06-29",
    "from": "2026-06-29T15:54:14",
    "to": "2026-06-29T15:54:14"
};

fetch(url, {
    method: "GET",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-showtimes">
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;The selected movie id is invalid. (and 3 more errors)&quot;,
    &quot;errors&quot;: {
        &quot;movie_id&quot;: [
            &quot;The selected movie id is invalid.&quot;
        ],
        &quot;cinema_id&quot;: [
            &quot;The selected cinema id is invalid.&quot;
        ],
        &quot;hall_id&quot;: [
            &quot;The selected hall id is invalid.&quot;
        ],
        &quot;tier_id&quot;: [
            &quot;The selected tier id is invalid.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-showtimes" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-showtimes"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-showtimes"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-showtimes" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-showtimes">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-showtimes" data-method="GET"
      data-path="api/showtimes"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-showtimes', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-showtimes"
                    onclick="tryItOut('GETapi-showtimes');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-showtimes"
                    onclick="cancelTryOut('GETapi-showtimes');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-showtimes"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/showtimes</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-showtimes"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-showtimes"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>movie_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="movie_id"                data-endpoint="GETapi-showtimes"
               value="1"
               data-component="query">
    <br>
<p>Filter by movie. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>cinema_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="cinema_id"                data-endpoint="GETapi-showtimes"
               value="1"
               data-component="query">
    <br>
<p>Filter by cinema. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>hall_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="hall_id"                data-endpoint="GETapi-showtimes"
               value="1"
               data-component="query">
    <br>
<p>Filter by hall. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>tier_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="tier_id"                data-endpoint="GETapi-showtimes"
               value="1"
               data-component="query">
    <br>
<p>Filter by price tier. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>date</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="date"                data-endpoint="GETapi-showtimes"
               value="2026-06-29"
               data-component="query">
    <br>
<p>Filter by calendar day (Y-m-d). Example: <code>2026-06-29</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>movie_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="movie_id"                data-endpoint="GETapi-showtimes"
               value="16"
               data-component="body">
    <br>
<p>Must match an existing stored value. Example: <code>16</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>cinema_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="cinema_id"                data-endpoint="GETapi-showtimes"
               value="16"
               data-component="body">
    <br>
<p>Must match an existing stored value. Example: <code>16</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>hall_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="hall_id"                data-endpoint="GETapi-showtimes"
               value="16"
               data-component="body">
    <br>
<p>Must match an existing stored value. Example: <code>16</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>tier_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="tier_id"                data-endpoint="GETapi-showtimes"
               value="16"
               data-component="body">
    <br>
<p>Must match an existing stored value. Example: <code>16</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>date</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="date"                data-endpoint="GETapi-showtimes"
               value="2026-06-29"
               data-component="body">
    <br>
<p>Must be a valid date in the format <code>Y-m-d</code>. Example: <code>2026-06-29</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>from</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="from"                data-endpoint="GETapi-showtimes"
               value="2026-06-29T15:54:14"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-06-29T15:54:14</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>to</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="to"                data-endpoint="GETapi-showtimes"
               value="2026-06-29T15:54:14"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-06-29T15:54:14</code></p>
        </div>
        </form>

                    <h2 id="showtimes-GETapi-showtimes--showtime_id--seats">Get the seat map for a showtime.</h2>

<p>
</p>

<p>Derives per-seat status (available | held | booked) for this showtime and resolves
each seat's price via lookup(tier, seat.type). Money is integer minor units in RM.</p>

<span id="example-requests-GETapi-showtimes--showtime_id--seats">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/showtimes/1/seats" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/showtimes/1/seats"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-showtimes--showtime_id--seats">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;showtime&quot;: {
            &quot;id&quot;: 1,
            &quot;movie_id&quot;: 1,
            &quot;hall_id&quot;: 1,
            &quot;tier_id&quot;: 1,
            &quot;starts_at&quot;: &quot;2026-07-02T19:30:00+00:00&quot;,
            &quot;ends_at&quot;: &quot;2026-07-02T21:19:00+00:00&quot;,
            &quot;cinema&quot;: {
                &quot;id&quot;: 1,
                &quot;name&quot;: &quot;TGV Suria KLCC&quot;
            },
            &quot;hall_name&quot;: &quot;Hall 1&quot;
        },
        &quot;tier&quot;: {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;Classic&quot;,
            &quot;currency&quot;: &quot;RM&quot;,
            &quot;prices&quot;: [
                {
                    &quot;seat_type&quot;: &quot;standard&quot;,
                    &quot;price&quot;: 1800
                },
                {
                    &quot;seat_type&quot;: &quot;premium&quot;,
                    &quot;price&quot;: 2500
                }
            ]
        },
        &quot;seats&quot;: [
            {
                &quot;id&quot;: 1,
                &quot;seat_code&quot;: &quot;A1&quot;,
                &quot;row_label&quot;: &quot;A&quot;,
                &quot;col_num&quot;: 1,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;booked&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 2,
                &quot;seat_code&quot;: &quot;A2&quot;,
                &quot;row_label&quot;: &quot;A&quot;,
                &quot;col_num&quot;: 2,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;booked&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 3,
                &quot;seat_code&quot;: &quot;A3&quot;,
                &quot;row_label&quot;: &quot;A&quot;,
                &quot;col_num&quot;: 3,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 4,
                &quot;seat_code&quot;: &quot;A4&quot;,
                &quot;row_label&quot;: &quot;A&quot;,
                &quot;col_num&quot;: 4,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 5,
                &quot;seat_code&quot;: &quot;A5&quot;,
                &quot;row_label&quot;: &quot;A&quot;,
                &quot;col_num&quot;: 5,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 6,
                &quot;seat_code&quot;: &quot;A6&quot;,
                &quot;row_label&quot;: &quot;A&quot;,
                &quot;col_num&quot;: 6,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 7,
                &quot;seat_code&quot;: &quot;A7&quot;,
                &quot;row_label&quot;: &quot;A&quot;,
                &quot;col_num&quot;: 7,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 8,
                &quot;seat_code&quot;: &quot;A8&quot;,
                &quot;row_label&quot;: &quot;A&quot;,
                &quot;col_num&quot;: 8,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 9,
                &quot;seat_code&quot;: &quot;A9&quot;,
                &quot;row_label&quot;: &quot;A&quot;,
                &quot;col_num&quot;: 9,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 10,
                &quot;seat_code&quot;: &quot;A10&quot;,
                &quot;row_label&quot;: &quot;A&quot;,
                &quot;col_num&quot;: 10,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 11,
                &quot;seat_code&quot;: &quot;B1&quot;,
                &quot;row_label&quot;: &quot;B&quot;,
                &quot;col_num&quot;: 1,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 12,
                &quot;seat_code&quot;: &quot;B2&quot;,
                &quot;row_label&quot;: &quot;B&quot;,
                &quot;col_num&quot;: 2,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 13,
                &quot;seat_code&quot;: &quot;B3&quot;,
                &quot;row_label&quot;: &quot;B&quot;,
                &quot;col_num&quot;: 3,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 14,
                &quot;seat_code&quot;: &quot;B4&quot;,
                &quot;row_label&quot;: &quot;B&quot;,
                &quot;col_num&quot;: 4,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 15,
                &quot;seat_code&quot;: &quot;B5&quot;,
                &quot;row_label&quot;: &quot;B&quot;,
                &quot;col_num&quot;: 5,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;booked&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 16,
                &quot;seat_code&quot;: &quot;B6&quot;,
                &quot;row_label&quot;: &quot;B&quot;,
                &quot;col_num&quot;: 6,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;booked&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 17,
                &quot;seat_code&quot;: &quot;B7&quot;,
                &quot;row_label&quot;: &quot;B&quot;,
                &quot;col_num&quot;: 7,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 18,
                &quot;seat_code&quot;: &quot;B8&quot;,
                &quot;row_label&quot;: &quot;B&quot;,
                &quot;col_num&quot;: 8,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 19,
                &quot;seat_code&quot;: &quot;B9&quot;,
                &quot;row_label&quot;: &quot;B&quot;,
                &quot;col_num&quot;: 9,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 20,
                &quot;seat_code&quot;: &quot;B10&quot;,
                &quot;row_label&quot;: &quot;B&quot;,
                &quot;col_num&quot;: 10,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 21,
                &quot;seat_code&quot;: &quot;C1&quot;,
                &quot;row_label&quot;: &quot;C&quot;,
                &quot;col_num&quot;: 1,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 22,
                &quot;seat_code&quot;: &quot;C2&quot;,
                &quot;row_label&quot;: &quot;C&quot;,
                &quot;col_num&quot;: 2,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 23,
                &quot;seat_code&quot;: &quot;C3&quot;,
                &quot;row_label&quot;: &quot;C&quot;,
                &quot;col_num&quot;: 3,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 24,
                &quot;seat_code&quot;: &quot;C4&quot;,
                &quot;row_label&quot;: &quot;C&quot;,
                &quot;col_num&quot;: 4,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 25,
                &quot;seat_code&quot;: &quot;C5&quot;,
                &quot;row_label&quot;: &quot;C&quot;,
                &quot;col_num&quot;: 5,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;held&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 26,
                &quot;seat_code&quot;: &quot;C6&quot;,
                &quot;row_label&quot;: &quot;C&quot;,
                &quot;col_num&quot;: 6,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;held&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 27,
                &quot;seat_code&quot;: &quot;C7&quot;,
                &quot;row_label&quot;: &quot;C&quot;,
                &quot;col_num&quot;: 7,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 28,
                &quot;seat_code&quot;: &quot;C8&quot;,
                &quot;row_label&quot;: &quot;C&quot;,
                &quot;col_num&quot;: 8,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 29,
                &quot;seat_code&quot;: &quot;C9&quot;,
                &quot;row_label&quot;: &quot;C&quot;,
                &quot;col_num&quot;: 9,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 30,
                &quot;seat_code&quot;: &quot;C10&quot;,
                &quot;row_label&quot;: &quot;C&quot;,
                &quot;col_num&quot;: 10,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 31,
                &quot;seat_code&quot;: &quot;D1&quot;,
                &quot;row_label&quot;: &quot;D&quot;,
                &quot;col_num&quot;: 1,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 32,
                &quot;seat_code&quot;: &quot;D2&quot;,
                &quot;row_label&quot;: &quot;D&quot;,
                &quot;col_num&quot;: 2,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 33,
                &quot;seat_code&quot;: &quot;D3&quot;,
                &quot;row_label&quot;: &quot;D&quot;,
                &quot;col_num&quot;: 3,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 34,
                &quot;seat_code&quot;: &quot;D4&quot;,
                &quot;row_label&quot;: &quot;D&quot;,
                &quot;col_num&quot;: 4,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;held&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 35,
                &quot;seat_code&quot;: &quot;D5&quot;,
                &quot;row_label&quot;: &quot;D&quot;,
                &quot;col_num&quot;: 5,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 36,
                &quot;seat_code&quot;: &quot;D6&quot;,
                &quot;row_label&quot;: &quot;D&quot;,
                &quot;col_num&quot;: 6,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 37,
                &quot;seat_code&quot;: &quot;D7&quot;,
                &quot;row_label&quot;: &quot;D&quot;,
                &quot;col_num&quot;: 7,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 38,
                &quot;seat_code&quot;: &quot;D8&quot;,
                &quot;row_label&quot;: &quot;D&quot;,
                &quot;col_num&quot;: 8,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 39,
                &quot;seat_code&quot;: &quot;D9&quot;,
                &quot;row_label&quot;: &quot;D&quot;,
                &quot;col_num&quot;: 9,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 40,
                &quot;seat_code&quot;: &quot;D10&quot;,
                &quot;row_label&quot;: &quot;D&quot;,
                &quot;col_num&quot;: 10,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 41,
                &quot;seat_code&quot;: &quot;E1&quot;,
                &quot;row_label&quot;: &quot;E&quot;,
                &quot;col_num&quot;: 1,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 42,
                &quot;seat_code&quot;: &quot;E2&quot;,
                &quot;row_label&quot;: &quot;E&quot;,
                &quot;col_num&quot;: 2,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 43,
                &quot;seat_code&quot;: &quot;E3&quot;,
                &quot;row_label&quot;: &quot;E&quot;,
                &quot;col_num&quot;: 3,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 44,
                &quot;seat_code&quot;: &quot;E4&quot;,
                &quot;row_label&quot;: &quot;E&quot;,
                &quot;col_num&quot;: 4,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 45,
                &quot;seat_code&quot;: &quot;E5&quot;,
                &quot;row_label&quot;: &quot;E&quot;,
                &quot;col_num&quot;: 5,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 46,
                &quot;seat_code&quot;: &quot;E6&quot;,
                &quot;row_label&quot;: &quot;E&quot;,
                &quot;col_num&quot;: 6,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 47,
                &quot;seat_code&quot;: &quot;E7&quot;,
                &quot;row_label&quot;: &quot;E&quot;,
                &quot;col_num&quot;: 7,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 48,
                &quot;seat_code&quot;: &quot;E8&quot;,
                &quot;row_label&quot;: &quot;E&quot;,
                &quot;col_num&quot;: 8,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 49,
                &quot;seat_code&quot;: &quot;E9&quot;,
                &quot;row_label&quot;: &quot;E&quot;,
                &quot;col_num&quot;: 9,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 50,
                &quot;seat_code&quot;: &quot;E10&quot;,
                &quot;row_label&quot;: &quot;E&quot;,
                &quot;col_num&quot;: 10,
                &quot;type&quot;: &quot;standard&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 1800,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 51,
                &quot;seat_code&quot;: &quot;F1&quot;,
                &quot;row_label&quot;: &quot;F&quot;,
                &quot;col_num&quot;: 1,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 52,
                &quot;seat_code&quot;: &quot;F2&quot;,
                &quot;row_label&quot;: &quot;F&quot;,
                &quot;col_num&quot;: 2,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 53,
                &quot;seat_code&quot;: &quot;F3&quot;,
                &quot;row_label&quot;: &quot;F&quot;,
                &quot;col_num&quot;: 3,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 54,
                &quot;seat_code&quot;: &quot;F4&quot;,
                &quot;row_label&quot;: &quot;F&quot;,
                &quot;col_num&quot;: 4,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 55,
                &quot;seat_code&quot;: &quot;F5&quot;,
                &quot;row_label&quot;: &quot;F&quot;,
                &quot;col_num&quot;: 5,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 56,
                &quot;seat_code&quot;: &quot;F6&quot;,
                &quot;row_label&quot;: &quot;F&quot;,
                &quot;col_num&quot;: 6,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 57,
                &quot;seat_code&quot;: &quot;F7&quot;,
                &quot;row_label&quot;: &quot;F&quot;,
                &quot;col_num&quot;: 7,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 58,
                &quot;seat_code&quot;: &quot;F8&quot;,
                &quot;row_label&quot;: &quot;F&quot;,
                &quot;col_num&quot;: 8,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 59,
                &quot;seat_code&quot;: &quot;F9&quot;,
                &quot;row_label&quot;: &quot;F&quot;,
                &quot;col_num&quot;: 9,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 60,
                &quot;seat_code&quot;: &quot;F10&quot;,
                &quot;row_label&quot;: &quot;F&quot;,
                &quot;col_num&quot;: 10,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 61,
                &quot;seat_code&quot;: &quot;G1&quot;,
                &quot;row_label&quot;: &quot;G&quot;,
                &quot;col_num&quot;: 1,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 62,
                &quot;seat_code&quot;: &quot;G2&quot;,
                &quot;row_label&quot;: &quot;G&quot;,
                &quot;col_num&quot;: 2,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 63,
                &quot;seat_code&quot;: &quot;G3&quot;,
                &quot;row_label&quot;: &quot;G&quot;,
                &quot;col_num&quot;: 3,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 64,
                &quot;seat_code&quot;: &quot;G4&quot;,
                &quot;row_label&quot;: &quot;G&quot;,
                &quot;col_num&quot;: 4,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 65,
                &quot;seat_code&quot;: &quot;G5&quot;,
                &quot;row_label&quot;: &quot;G&quot;,
                &quot;col_num&quot;: 5,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 66,
                &quot;seat_code&quot;: &quot;G6&quot;,
                &quot;row_label&quot;: &quot;G&quot;,
                &quot;col_num&quot;: 6,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 67,
                &quot;seat_code&quot;: &quot;G7&quot;,
                &quot;row_label&quot;: &quot;G&quot;,
                &quot;col_num&quot;: 7,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 68,
                &quot;seat_code&quot;: &quot;G8&quot;,
                &quot;row_label&quot;: &quot;G&quot;,
                &quot;col_num&quot;: 8,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 69,
                &quot;seat_code&quot;: &quot;G9&quot;,
                &quot;row_label&quot;: &quot;G&quot;,
                &quot;col_num&quot;: 9,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 70,
                &quot;seat_code&quot;: &quot;G10&quot;,
                &quot;row_label&quot;: &quot;G&quot;,
                &quot;col_num&quot;: 10,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 71,
                &quot;seat_code&quot;: &quot;H1&quot;,
                &quot;row_label&quot;: &quot;H&quot;,
                &quot;col_num&quot;: 1,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 72,
                &quot;seat_code&quot;: &quot;H2&quot;,
                &quot;row_label&quot;: &quot;H&quot;,
                &quot;col_num&quot;: 2,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 73,
                &quot;seat_code&quot;: &quot;H3&quot;,
                &quot;row_label&quot;: &quot;H&quot;,
                &quot;col_num&quot;: 3,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 74,
                &quot;seat_code&quot;: &quot;H4&quot;,
                &quot;row_label&quot;: &quot;H&quot;,
                &quot;col_num&quot;: 4,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 75,
                &quot;seat_code&quot;: &quot;H5&quot;,
                &quot;row_label&quot;: &quot;H&quot;,
                &quot;col_num&quot;: 5,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 76,
                &quot;seat_code&quot;: &quot;H6&quot;,
                &quot;row_label&quot;: &quot;H&quot;,
                &quot;col_num&quot;: 6,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 77,
                &quot;seat_code&quot;: &quot;H7&quot;,
                &quot;row_label&quot;: &quot;H&quot;,
                &quot;col_num&quot;: 7,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 78,
                &quot;seat_code&quot;: &quot;H8&quot;,
                &quot;row_label&quot;: &quot;H&quot;,
                &quot;col_num&quot;: 8,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;available&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 79,
                &quot;seat_code&quot;: &quot;H9&quot;,
                &quot;row_label&quot;: &quot;H&quot;,
                &quot;col_num&quot;: 9,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;booked&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            },
            {
                &quot;id&quot;: 80,
                &quot;seat_code&quot;: &quot;H10&quot;,
                &quot;row_label&quot;: &quot;H&quot;,
                &quot;col_num&quot;: 10,
                &quot;type&quot;: &quot;premium&quot;,
                &quot;status&quot;: &quot;booked&quot;,
                &quot;price&quot;: 2500,
                &quot;currency&quot;: &quot;RM&quot;
            }
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-showtimes--showtime_id--seats" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-showtimes--showtime_id--seats"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-showtimes--showtime_id--seats"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-showtimes--showtime_id--seats" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-showtimes--showtime_id--seats">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-showtimes--showtime_id--seats" data-method="GET"
      data-path="api/showtimes/{showtime_id}/seats"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-showtimes--showtime_id--seats', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-showtimes--showtime_id--seats"
                    onclick="tryItOut('GETapi-showtimes--showtime_id--seats');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-showtimes--showtime_id--seats"
                    onclick="cancelTryOut('GETapi-showtimes--showtime_id--seats');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-showtimes--showtime_id--seats"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/showtimes/{showtime_id}/seats</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-showtimes--showtime_id--seats"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-showtimes--showtime_id--seats"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>showtime_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="showtime_id"                data-endpoint="GETapi-showtimes--showtime_id--seats"
               value="1"
               data-component="url">
    <br>
<p>The ID of the showtime. Example: <code>1</code></p>
            </div>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-showtimes--showtime_id--seats"
               value="1"
               data-component="url">
    <br>
<p>The showtime id. Example: <code>1</code></p>
            </div>
                    </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                            </div>
            </div>
</div>
</body>
</html>

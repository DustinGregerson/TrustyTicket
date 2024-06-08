<main>
        <div class="container">
            <div class="left">
                <h1>Create a free <br>
                    account to start <br>
                    hosting an event 
                </h1>
                <form id="target" method="post" action="ServiceProvider/API.php">
                    <input type="hidden" name="api_function_call" value="register">

                    <span id="username_error"></span>
                    <input type="text" placeholder="Your user name" required name="username">

                    <span id="password_error"></span>
                    <input type="password" placeholder="Your password" required name="password">

                    <span id="email_error"></span>
                    <input type="email" placeholder="Your email address" required name="email">
                    
                    <button type="submit">Next</button>
                </form>
                <p>Already have an account? <a href="?action=login">Sign in</a></p>
            </div>
            <div class="right"></div>
        </div>
    </main>
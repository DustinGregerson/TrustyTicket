<main>
        <div class="container">
            <div class="left">
                <h1>Sign in</h1>
                <p>Welcome to TrustyTicket</p>
                <form id="target" method="post" action="ServiceProvider/API.php">
                    <input id="api_function_call" type="hidden" name="api_function_call" value="login">
                    <input id="username" type="text" placeholder="Your username" name="username" required>
                    <input id="password" type="password" placeholder="Your password" name="password" required>
                    <button type="submit">Next</button>
                </form>
                <p>Don't have an account yet? <a href="?action=register">Sign up</a></p>
            </div>
            <div class="right"></div>
        </div>
</main>
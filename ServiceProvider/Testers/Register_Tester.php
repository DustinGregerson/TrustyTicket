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

<input type="text" placeholder="Your First Name" required name="first_name">

<input type="text" placeholder="Your Last Name" required name="last_name">

<label for="state">State</label>
<select name="state" id="state">
    <option value="AL">AL</option>
    <option value="AK">AK</option>
    <option value="AZ">AZ</option>
    <option value="AR">AR</option>
    <option value="CA">CA</option>
    <option value="CO">CO</option>
    <option value="CT">CT</option>
    <option value="DE">DE</option>
    <option value="FL">FL</option>
    <option value="GA">GA</option>
    <option value="HI">HI</option>
    <option value="ID">ID</option>
    <option value="IL">IL</option>
    <option value="IN">IN</option>
    <option value="IA">IA</option>
    <option value="KS">KS</option>
    <option value="KY">KY</option>
    <option value="LA">LA</option>
    <option value="ME">ME</option>
    <option value="MD">MD</option>
    <option value="MA">MA</option>
    <option value="MI">MI</option>
    <option value="MN">MN</option>
    <option value="MS">MS</option>
    <option value="MO">MO</option>
    <option value="MT">MT</option>
    <option value="NE">NE</option>
    <option value="NV">NV</option>
    <option value="NH">NH</option>
    <option value="NJ">NJ</option>
    <option value="NM">NM</option>
    <option value="NY">NY</option>
    <option value="NC">NC</option>
    <option value="ND">ND</option>
    <option value="OH">OH</option>
    <option value="OK">OK</option>
    <option value="OR">OR</option>
    <option value="PA">PA</option>
    <option value="RI">RI</option>
    <option value="SC">SC</option>
    <option value="SD">SD</option>
    <option value="TN">TN</option>
    <option value="TX">TX</option>
    <option value="UT">UT</option>
    <option value="VT">VT</option>
    <option value="VA">VA</option>
    <option value="WA">WA</option>
    <option value="WV">WV</option>
    <option value="WI">WI</option>
    <option value="WY">WY</option>
  </select>
<br>

<button type="submit">Next</button>
                </form>
                <p>Already have an account? <a href="?action=login">Sign in</a></p>
            </div>
            <div class="right"></div>
        </div>
    </main>
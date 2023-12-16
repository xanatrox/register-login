{extends file='index.tpl'}
{block name=title}Login{/block}
{block name=content}
    {if isset($errors) && count($errors) > 0}
        <ul style="color: red;">
            {foreach $errors as $error}
                <li>{$error}</li>
            {/foreach}
        </ul>
    {/if}
        <form method="post" action="login">
            <div>
                <label for="email">E-Mail</label>
                <input type="text" id="email" name="email" placeholder="Enter e-mail" value="{$values.email|escape|default:''}">
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password">
            </div>
            <button type="submit">Submit</button>
        </form>
{/block}
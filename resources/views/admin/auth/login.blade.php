@extends('layouts.admin.app')

<div class="row center-xs" style="padding-top: 30px; ">
  <div class="col-xs-6">
    <form class="ui large form" action="/admin/login" method="POST">
      <div class="ui stacked segment">
        <div class="field">
          <div class="ui left icon input">
            <i class="user icon"></i>
            <input type="text" name="email" placeholder="E-mail address">
          </div>
        </div>
        <div class="field">
          <div class="ui left icon input">
            <i class="lock icon"></i>
            <input type="password" name="password" placeholder="Password">
          </div>
        </div>
        <button class="ui fluid large teal submit button" type="submit">Login</button>
      </div>
    </form>
  </div>
</div>
<?php startup_gettext(); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Tiny Tiny RSS : Login</title>
	<link rel="shortcut icon" type="image/png" href="images/favicon.png">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<?php
	foreach (array("lib/prototype.js",
				"lib/dojo/dojo.js",
				"lib/dojo/tt-rss-layer.js",
				"lib/prototype.js",
			 	"js/common.js",
				"js/utility.js",
				"errors.php?mode=js") as $jsfile) {

		echo javascript_tag($jsfile);

	} ?>

	<?php if (theme_exists(LOCAL_OVERRIDE_STYLESHEET)) {
		echo stylesheet_tag(get_theme_path(LOCAL_OVERRIDE_STYLESHEET));
	} ?>

	<style type="text/css">
		@media (prefers-color-scheme: dark) {
			body {
				background : #303030;
			}
		}

		body.css_loading * {
			display : none;
		}
	</style>

	<script type="text/javascript">
		require({cache:{}});
	</script>
</head>

<body class="flat ttrss_utility ttrss_login css_loading">

<script type="text/javascript">
	const UtilityApp = {
        previousLogin: "",
	    init: function() { /* invoked by UtilityJS */
            require(['dojo/parser', "dojo/ready", 'dijit/form/Button','dijit/form/CheckBox', 'dijit/form/Form',
                'dijit/form/Select','dijit/form/TextBox','dijit/form/ValidationTextBox'],function(parser, ready){
                ready(function() {
					parser.parse();

					dijit.byId("bw_limit").attr("checked", Cookie.get("ttrss_bwlimit") == 'true');
					dijit.byId("login").focus();
                });
            });
		},
        fetchProfiles: function() {
	        const login = dijit.byId("login").attr('value');

	        if (login && login != this.previousLogin) {
                this.previousLogin = login;

                xhrJson("public.php", {op: "getprofiles", login: login},
                    (reply) => {
                        const profile = dijit.byId('profile');

                        profile.removeOption(profile.getOptions());

                        reply.each((p) => {
                            profile
                                .attr("disabled", false)
                                .addOption(p);
                        });
                    });
            }
	    },
        gotoRegForm: function() {
        	window.location.href = "register.php";
        	return false;
    	},
        bwLimitChange: function(elem) {
        	Cookie.set("ttrss_bwlimit", elem.checked,
				<?php print SESSION_COOKIE_LIFETIME ?>);
	    }
    };


</script>

<?php $return = urlencode(make_self_url()) ?>

<div class="container">

	<h1><?php echo "Authentication" ?></h1>
	<div class="content">
		<form action="public.php?return=<?php echo $return ?>"
			  dojoType="dijit.form.Form" method="POST">

			<?php print_hidden("op", "login"); ?>

			<?php if (!empty($_SESSION["login_error_msg"])) { ?>
				<?php echo format_error($_SESSION["login_error_msg"]) ?>
				<?php $_SESSION["login_error_msg"] = ""; ?>
			<?php } ?>

			<fieldset>
				<label><?php echo __("Login:") ?></label>
				<input name="login" id="login" dojoType="dijit.form.TextBox" type="text"
					   onchange="UtilityApp.fetchProfiles()"
					   onfocus="UtilityApp.fetchProfiles()"
					   onblur="UtilityApp.fetchProfiles()"
					   required="1" value="<?php echo $_SESSION["fake_login"] ?? "" ?>" />
			</fieldset>

			<fieldset>
				<label><?php echo __("Password:") ?></label>

				<input type="password" name="password" required="1"
					   dojoType="dijit.form.TextBox"
					   class="input input-text"
					   onchange="UtilityApp.fetchProfiles()"
					   onfocus="UtilityApp.fetchProfiles()"
					   onblur="UtilityApp.fetchProfiles()"
					   value="<?php echo $_SESSION["fake_password"] ?? "" ?>"/>
			</fieldset>
			<?php if (strpos(PLUGINS, "auth_internal") !== false) { ?>
				<fieldset class="align-right">
					<a href="public.php?op=forgotpass"><?php echo __("I forgot my password") ?></a>
				</fieldset>
			<?php } ?>

			<fieldset>
				<label><?php echo __("Profile:") ?></label>

				<select disabled='disabled' name="profile" id="profile" dojoType='dijit.form.Select'>
					<option><?php echo __("Default profile") ?></option>
				</select>
			</fieldset>

			<fieldset class="narrow">
				<label> </label>

				<label id="bw_limit_label"><input dojoType="dijit.form.CheckBox" name="bw_limit" id="bw_limit"
					  type="checkbox" onchange="UtilityApp.bwLimitChange(this)">
					<?php echo __("Use less traffic") ?></label>
			</fieldset>

			<div dojoType="dijit.Tooltip" connectId="bw_limit_label" position="below" style="display:none">
				<?php echo __("Does not display images in articles, reduces automatic refreshes."); ?>
			</div>

			<fieldset class="narrow">
				<label> </label>

				<label id="safe_mode_label"><input dojoType="dijit.form.CheckBox" name="safe_mode" id="safe_mode"
					  type="checkbox">
					<?php echo __("Safe mode") ?></label>
			</fieldset>

			<div dojoType="dijit.Tooltip" connectId="safe_mode_label" position="below" style="display:none">
				<?php echo __("Uses default theme and prevents all plugins from loading."); ?>
			</div>
			<?php if (SESSION_COOKIE_LIFETIME > 0) { ?>

				<fieldset class="narrow">
					<label> </label>
					<label>
						<input dojoType="dijit.form.CheckBox" name="remember_me" id="remember_me" type="checkbox">
						<?php echo __("Remember me") ?>
					</label>
				</fieldset>

			<?php } ?>

			<hr/>

			<fieldset class="align-right">
				<label> </label>

				<button dojoType="dijit.form.Button" type="submit" class="alt-primary"><?php echo __('Log in') ?></button>

				<?php if (defined('ENABLE_REGISTRATION') && ENABLE_REGISTRATION) { ?>
					<button onclick="return UtilityApp.gotoRegForm()" dojoType="dijit.form.Button">
						<?php echo __("Create new account") ?></button>
				<?php } ?>
			</fieldset>

		</form>
	</div>

	<div class="footer">
		<a href="https://tt-rss.org/">Tiny Tiny RSS</a>
		&copy; 2005&ndash;<?php echo date('Y') ?> <a href="https://fakecake.org/">Andrew Dolgov</a>
	</div>

</div>

</body>
</html>

{*
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="panel">
	<h3><i class="icon icon-link"></i> {l s='Connect' mod='powerise'}</h3>
	<div class="row">
		<div class="col-md-6">
			<h4>{l s='Connect your shop with Powerise account and start using the power of AI' mod='powerise'}</h4>
		</div>
		<div class="col-md-6 text-right">
			{if $auth_user_email}
				<a href="https://app.powerise.io" class="btn btn-primary">{l s='Proceed to configuration' mod='powerise'}</a>
				<hr>
				<p><strong>{l s='You are connected as' mod='powerise'}</strong> {$auth_user_firstname} {$auth_user_lastname} ({$auth_user_email})</p>
				<p><a href="#" id="powerise_connect">{l s='Do you want to change account?' mod='powerise'}</a></p>
			{else}
				<a href="#"  id="powerise_connect" class="btn btn-primary">{l s='Connect' mod='powerise'}</a>
			{/if}
		</div>
	</div>
</div>

{if $configuration_disabled}
	<div class="pw-section pw-section--disabled">
		{$configuration_form}
		<div class="pw-section__overlay">Connect your account first</div>
	</div>
{else}
	{$configuration_form}
{/if}

<script src="//powerise.io/statics/powerisejs/@latest/index.js?t=1682577896"></script>
<script>
	$btn = document.getElementById('powerise_connect')
	$btn.addEventListener('click', (e) => {
		e.preventDefault();

		// Powerise.Environment.setDebugMode(true);
		Powerise.connect({
			shopUrl: '{$shop_url}',
			ref: '{$redirect_url}',
			platform: 'PrestaShop'
		}).then((payload) => {
			const formBody = [];

			Object.keys(payload).forEach((item) => {
				const key = encodeURIComponent(item);
				const value = encodeURIComponent(payload[item]);
				formBody.push(key+"="+value);
			});

			fetch(window.location.href, {
				method: 'POST',
				body: formBody.join("&"),
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded'
				}
			})
			.then(response => response.text())
			.then(data => {
				window.location.reload();
			})
			.catch(error => console.error(error));
			console.log(payload);
		});
	});
</script>

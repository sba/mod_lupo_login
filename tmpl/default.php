<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_lupo_login
 *
 * @copyright   Copyright (C) databauer / Stefan Bauer
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
?>
<style type="text/css">

</style>

<script type="text/javascript">
	jQuery(function($) {
		$('.btn-prolong').click(function(){
			var $button = $(this);
			$.ajax({
				method: "POST",
				url: "index.php?option=com_lupo&task=prolong&format=raw",
				data: { lupo_id: $button.data('lupo_id')}
			})
				.done(function( msg ) {
					if(msg=='error') {
						$button.html('<i class="uk-icon-exclamation-circle red"></i> ERROR');
					} else {
						$button.html('<i class="uk-icon-check"></i> <?php echo JText::_('MOD_LUPO_LOGIN_WAS_PROLONGED') ?>');
						$button.parent().parent().find('.retour-date').html('<b>'+msg+'</b>');
					}
					$button.attr('disabled', 'disabled'); //disable anyway
				});
		})
	})
</script>

<div class="mod_lupo_login <?php echo $moduleclass_sfx ?>">
	<?php
	if(!$client){
		echo $module->content;
	}?>

	<form method="post" action="<?=JURI::current()?>" class="uk-form">
		<div class="uk-grid uk-form-row" data-uk-grid-margin>
			<?php
			$session = JFactory::getSession();
			$client = $session->get('lupo_client');
			if(isset($client)) {
				$hasAbo = $client->aboenddat != "0000-00-00";
			}
			if($client) { ?>
				<input type="hidden" name="lupo_clientlogin" value="logout">
                <?php if($hasAbo){ ?>
				<div class="uk-width-2-3"><?php echo JText::_('MOD_LUPO_LOGIN_ABO_VALID_UNTIL') ?><b>: <?php echo date("d.m.Y", strtotime($client->aboenddat))?></b> (<?php echo $client->abotype?>)</div>
                <?php } ?>
				<div class="uk-width-1-<?=$hasAbo?'3':'1'?>"><button type="submit" name="Submit" value="<?php echo JText::_('MOD_LUPO_LOGIN_LOGOUT') ?>" class="uk-button uk-button-primary uk-float-right"><?php echo JText::_('MOD_LUPO_LOGIN_LOGOUT') ?></button></div>
                <style> .lupo_show_logoff { display: none; } .lupo_show_logon { display: block; } </style>
			<?php } else { ?>
				<input type="hidden" name="lupo_clientlogin" value="login">
				<div class="uk-width-1-1 uk-width-small-1-3"><input type="text" placeholder="<?php echo JText::_('MOD_LUPO_LOGIN_ADRNR') ?>" size="10" name="adrnr" class="uk-width-1-1" required></div>
				<div class="uk-width-1-1 uk-width-small-1-3"><input type="password" placeholder="<?php echo JText::_('MOD_LUPO_LOGIN_PASSWORD') ?>" size="10" name="password" class="uk-width-1-1" required></div>
				<div class="uk-width-1-1 uk-width-small-1-3"><button type="submit" name="Submit" value="<?php echo JText::_('MOD_LUPO_LOGIN_LOGIN') ?>" class="uk-button uk-button-primary"><?php echo JText::_('MOD_LUPO_LOGIN_LOGIN') ?></button></div>
                <style> .lupo_show_logoff { display: block; } .lupo_show_logon { display: none; } </style>
			<?php } ?>
		</div>
	</form>

	<?php if(isset($_GET['loginError'])){ ?>
		<div data-uk-alert="" class="uk-alert uk-alert-danger">
			<a class="uk-alert-close uk-close" href=""></a>
			<p><b><?php echo JText::_('MOD_LUPO_LOGIN_LOGIN_FAILED') ?></b> <?php echo JText::_('MOD_LUPO_LOGIN_LOGIN_USER_OR_PW_WRONG') ?></p>
		</div>
	<?php } ?>

	<?php if(isset($toylist) && count($toylist)>0){ ?>
		<h3><?php echo JText::_('MOD_LUPO_LOGIN_YOUR_TOYS') ?>:</h3>
		<table class="uk-table uk-table-striped">
			<tr>
			<th class="uk-hidden-small"><?php echo JText::_('MOD_LUPO_LOGIN_ARTNR') ?></th>
			<th><?php echo JText::_('MOD_LUPO_LOGIN_TOY') ?></th>
			<th><?php echo JText::_('MOD_LUPO_LOGIN_RETOUR_DATE') ?></th>
			<?php if( $allow_prolongation ) { ?>
			<th></th>
			<?php } ?>
			</tr>
		<?php foreach ($toylist as $toy){?>
			<tr>
				<td class="uk-hidden-small"><?=str_replace('.0', '', $toy->number) ?></td>
				<td><a href="<?=$toy->link?>"><?=$toy->title?></a></td>
				<td class="retour-date">
					<?=date('d.m.Y', strtotime($toy->return_date))?>
					<?php
					if($toy->return_date <= date('Y-m-d')){?>
						<i class="uk-icon-exclamation red"></i>
					<?php } ?>
				</td>
				<?php
				if( $allow_prolongation ) { ?>
				<td>
					<?php
					if($toy->reminder_sent==1) {?>
						<i class="uk-float-right uk-text-danger"><?php echo JText::_('MOD_LUPO_LOGIN_REMINDER_SENT') ?></i>
						<?php
					} else {
						if ($toy->return_extended == 0) {
							if ($toy->prolongable == 0) { ?>
								<i class="uk-float-right uk-text-muted"><?php echo JText::_('MOD_LUPO_LOGIN_NOT_PROLONGABLE') ?></i>
							<?php } else { ?>
								<button class="uk-button uk-button-mini uk-float-right btn-prolong"
								        data-lupo_id="<?= $toy->lupo_id ?>"><?php echo JText::_('MOD_LUPO_LOGIN_PROLONG') ?> <?php echo ($toy->tax_extended > 0)? ' CHF '.number_format($toy->tax_extended, 2) :''; ?></button>
							<?php } ?>
						<?php } else { ?>
							<i class="uk-float-right"><?php echo JText::_('MOD_LUPO_LOGIN_WAS_PROLONGED') ?></i>
						<?php }
					}?>
				</td>
				<?php } ?>
			</tr>
		<?php } ?>
		</table>
	<?php }

	if(isset($client) && isset($toylist) && count($toylist)==0){ ?>
		<div class="uk-alert">Sie haben zur Zeit keine Spiele ausgeliehen.</div>
	<?php } ?>

</div>

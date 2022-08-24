<?php
/**
 * @package     LUPO
 * @copyright   Copyright (C) databauer / Stefan Bauer
 * @author      Stefan Bauer
 * @link        https://www.ludothekprogramm.ch
 * @license     License GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

$componentParams = JComponentHelper::getParams('com_lupo');

//load component language strings because we are in mod_lupo_login
//TODO: refactor language files ... move component strings to module?
$lang = JFactory::getLanguage();
$lang->load('com_lupo', JPATH_SITE, 'en-GB', true);
$lang->load('com_lupo', JPATH_SITE, $lang->getTag(), true);


?>
<style type="text/css">

</style>

<script type="text/javascript">
    jQuery(function ($) {
        $('.btn-prolong').click(function () {
            var $button = $(this);
            $.ajax({
                method: "POST",
                url: "index.php?option=com_lupo&task=prolong&format=raw",
                data: {lupo_id: $button.data('lupo_id')}
            })
                .done(function (msg) {
                    if (msg == 'error') {
                        $button.html('<i class="uk-icon-exclamation-circle red"></i> ERROR');
                    } else {
                        $button.html('<i class="uk-icon-check"></i> <?php echo JText::_('MOD_LUPO_LOGIN_WAS_PROLONGED') ?>');
                        $button.parent().parent().find('.retour-date').html('<b>' + msg + '</b>');
                    }
                    $button.attr('disabled', 'disabled'); //disable anyway
                });
        })

        $('.link-password-reset').click(function () {
            $('.form-password-reset').show();
        })

    })
</script>

<div class="mod_lupo_login <?php echo $moduleclass_sfx ?>">
	<?php
	if (!$client) {
		echo $module->content;
	} ?>

	<?php
	$show_login = $componentParams->get('detail_show_toy_res_hide_login', '0') == 0;
	if ($show_login) { ?>
		<?php
		$session = JFactory::getSession();
		$client  = $session->get('lupo_client');
		if (isset($client)) {
			$hasAbo = $client->aboenddat != "0000-00-00";
		}
		if ($client) { ?>
            <form method="post" action="<?= JURI::current() ?>" class="uk-form">
                <div class="uk-grid uk-form-row" data-uk-grid-margin>
                    <input type="hidden" name="lupo_clientlogin" value="logout">
					<?php if ($hasAbo) { ?>
                        <div class="uk-width-2-3"><?php echo JText::_('MOD_LUPO_LOGIN_ABO_VALID_UNTIL') ?><b>: <?php echo date("d.m.Y", strtotime($client->aboenddat)) ?></b> (<?php echo $client->abotype ?>)</div>
					<?php } ?>
                    <div class="uk-width-1-<?= $hasAbo ? '3' : '1' ?>">
                        <button type="submit" name="Submit" value="<?php echo JText::_('MOD_LUPO_LOGIN_LOGOUT') ?>" class="uk-button uk-button-primary uk-float-right"><i class="uk-icon-sign-out"></i> <?php echo JText::_('MOD_LUPO_LOGIN_LOGOUT') ?></button>
                    </div>
                </div>
            </form>
            <style>
                .lupo_show_logoff {
                    display: none;
                }

                .lupo_show_logon {
                    display: block;
                }
            </style>
		<?php } else { ?>
            <form method="post" action="<?= JURI::current() ?>" class="uk-form">
                <div class="uk-grid uk-form-row" data-uk-grid-margin>
                    <input type="hidden" name="lupo_clientlogin" value="login">
                    <div class="uk-width-1-1 uk-width-small-1-3"><input type="text" placeholder="<?php echo JText::_('MOD_LUPO_LOGIN_ADRNR') ?>" size="10" name="adrnr" class="uk-width-1-1" required></div>
                    <div class="uk-width-1-1 uk-width-small-1-3"><input type="password" placeholder="<?php echo JText::_('MOD_LUPO_LOGIN_PASSWORD') ?>" size="10" name="password" class="uk-width-1-1" required></div>
                    <div class="uk-width-1-1 uk-width-small-1-3">
                        <button type="submit" name="Submit" value="<?php echo JText::_('MOD_LUPO_LOGIN_LOGIN') ?>" class="uk-button uk-button-primary"><i class="uk-icon-sign-in"></i> <?php echo JText::_('MOD_LUPO_LOGIN_LOGIN') ?></button>
                    </div>
                </div>
            </form>
			<?php if ($password_reset_enabled) { ?>
                <div class="uk-width-1-1 uk-margin">
                    <a href="#" class="link-password-reset"><?php echo JText::_('MOD_LUPO_LOGIN_RESET') ?></a>
                </div>
                <form method="post" action="<?= JURI::current() ?>" class="uk-form">
                    <div class="uk-grid uk-form-row" data-uk-grid-margin>
                        <div class="uk-width-1-1 uk-width-small-2-3 form-password-reset" style="display: none">
                            <input type="email" placeholder="<?php echo JText::_('COM_LUPO_RES_EMAIL') ?>" name="email" class="uk-width-1-1" required>
                        </div>
                        <div class="uk-width-1-1 uk-width-small-1-3 form-password-reset" style="display: none">
                            <button type="submit" name="password-reset" value="password-reset" class="uk-button uk-button-primary"><?php echo JText::_('COM_LUPO_RES_SUBMIT') ?></button>
                        </div>
                    </div>
                </form>
				<?php
				if ($password_sent == 'mail_sent') {
					?>
                    <div class="uk-alert uk-alert-success">Es wurde eine E-Mail mit den Zugangsdaten gesendet.</div>
					<?php
				}
				if ($password_sent == 'mail_error') {
					?>
                    <div class="uk-alert uk-alert-success">Fehler: Die E-Mail konnte nicht versendet werden.</div>
					<?php
				}
				if ($password_sent == 'not_found') {
					?>
                    <div class="uk-alert uk-alert-danger">Fehler: E-Mailadresse unbekannt.</div>
					<?php
				}
				?>
			<?php } ?>

            <style>
                .lupo_show_logoff {
                    display: block;
                }

                .lupo_show_logon {
                    display: none;
                }
            </style>
		<?php } ?>


		<?php if (isset($_GET['loginError'])) { ?>
            <div data-uk-alert="" class="uk-alert uk-alert-danger">
                <a class="uk-alert-close uk-close" href=""></a>
                <p><b><?php echo JText::_('MOD_LUPO_LOGIN_LOGIN_FAILED') ?></b> <?php echo JText::_('MOD_LUPO_LOGIN_LOGIN_USER_OR_PW_WRONG') ?></p>
            </div>
		<?php } ?>

		<?php if (isset($toylist) && count($toylist) > 0) { ?>
            <h3><?php echo JText::_('MOD_LUPO_LOGIN_YOUR_TOYS') ?>:</h3>
            <table class="uk-table uk-table-striped">
                <tr>
                    <th class="uk-hidden-small"><?php echo JText::_('MOD_LUPO_LOGIN_ARTNR') ?></th>
                    <th><?php echo JText::_('MOD_LUPO_LOGIN_TOY') ?></th>
                    <th><?php echo JText::_('MOD_LUPO_LOGIN_RETOUR_DATE') ?></th>
					<?php if ($allow_prolongation) { ?>
                        <th class="uk-hidden-small"></th>
					<?php } ?>
                </tr>
				<?php foreach ($toylist as $toy) {
					if ($toy->reminder_sent == 1) {
						$html_prolongation = '<i class="uk-text-danger">' . JText::_('MOD_LUPO_LOGIN_REMINDER_SENT') . '</i>';
					} else {
						if ($toy->return_extended == 0) {
							$has_reservation = false;
							if ($toy->next_reservation != null) {
								$return_date_extended = new DateTime($toy->return_date_extended);
								$next_reservation     = new DateTime($toy->next_reservation);
								$interval             = $return_date_extended->diff($next_reservation);
								if ($interval->days <= 14 || $interval->invert == 1) {
									$has_reservation = true;
								}
							}

							$extended_date_over = $toy->return_date_extended < date("Y-m-d");

							if ($toy->prolongable == 0 || $has_reservation || $extended_date_over) {
								$html_prolongation = '<i class="uk-text-muted">' . JText::_('MOD_LUPO_LOGIN_NOT_PROLONGABLE') . '</i>';
							} else {
								$html_prolongation = '<button class="uk-button uk-button-mini btn-prolong" data-lupo_id="' . $toy->lupo_id . '">' . JText::_('MOD_LUPO_LOGIN_PROLONG') . ' ' . date("d.m.Y", strtotime($toy->return_date_extended)) . (($toy->tax_extended > 0) ? ' CHF ' . number_format($toy->tax_extended, 2) : '') . '</button>';
							}
						} else {
							$html_prolongation = '<i>' . JText::_('MOD_LUPO_LOGIN_WAS_PROLONGED') . '</i>';
						}
					} ?>
                    <tr>
                        <td class="uk-hidden-small"><?= str_replace('.0', '', $toy->number) ?></td>
                        <td>
                            <a href="<?= $toy->link ?>"><?= $toy->title ?></a>
							<?php
							if ($allow_prolongation) { ?>
                                <div class="uk-visible-small"><?php echo $html_prolongation ?></div>
							<?php } ?>
                        </td>
                        <td class="retour-date">
							<?= date('d.m.Y', strtotime($toy->return_date)) ?>
							<?php
							if ($toy->return_date < date('Y-m-d')) {
								?>
                                <i class="uk-icon-exclamation red"></i>
							<?php } ?>
                        </td>
						<?php
						if ($allow_prolongation) { ?>
                            <td class="uk-hidden-small" align="right">
								<?php echo $html_prolongation ?>
                            </td>
						<?php } ?>
                    </tr>
				<?php } ?>
            </table>
		<?php }

		if (isset($client) && isset($toylist) && count($toylist) == 0) { ?>
            <div class="uk-alert"><?php echo JText::_('MOD_LUPO_LOGIN_NO_TOYS') ?></div>
		<?php } ?>

	<?php } ?>

	<?php
	if ($reservations) {
		?>
        <div class="<?= $show_login ? 'uk-margin-large-top' : '' ?>" id="reservations">
            <h3><?= JText::_('MOD_LUPO_RESERVATIONS') ?></h3>
            <table class="uk-table uk-table-striped">
                <tr>
                    <th class="uk-hidden-small"><?php echo JText::_('MOD_LUPO_LOGIN_ARTNR') ?></th>
                    <th><?php echo JText::_('MOD_LUPO_LOGIN_TOY') ?></th>
                    <th></th>
                </tr>
				<?php
				$i = 0;
				foreach ($reservations as $reservation) {
					?>
                    <tr>
                        <td class="uk-hidden-small"><?= $reservation->toynr ?></td>
                        <td><?= $reservation->toyname ?></td>
                        <td class="uk-text-right">
                            <button class="uk-button uk-button-small btn-res-del" data-toyitem="<?= $i++ ?>" data-toynr="<?= $reservation->toynr ?>"><?php echo JText::_('JACTION_DELETE') ?></button>
                        </td>
                    </tr>

					<?php
				} ?>
            </table>
            <button class="uk-button uk-button-primary uk-align-right" data-uk-modal="{target:'#resform'}"><?php echo JText::_('JNEXT') ?> <i class="uk-icon-arrow-right"></i></button>
        </div>


	<?php
	//reservation
	$clientname  = ($client) ? $client->firstname . ' ' . $client->lastname : '';
	$clientnr    = ($client) ? $client->adrnr : '';
	$clientemail = ($client) ? $client->email : '';
	$clientphone = ($client) ? $client->phone : '';
	?>

        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('.btn-res-del').click(function () {
                    var $button = $(this);
                    $.ajax({
                        method: "POST",
                        url: "index.php?option=com_lupo&task=resdel&format=raw",
                        data: {toynr: $button.data('toynr')}
                    })
                        .done(function (response) {
                            response = JSON.parse(response);
                            if (response.msg == 'ok') {
                                if (response.reservations_nbr == 0) {
                                    $('.lupo_loginlink_reservations, #reservations').addClass('uk-hidden');
                                } else {
                                    $('#lupo_loginlink_reservations_nbr').html(response.reservations_nbr);
                                }
                                $button.parent().parent().remove();
                                $('#formtoyitem' + $button.data('toyitem')).remove(); //remove from list in form
                            } else {
                                $button.html('<i class="uk-icon-exclamation-circle red"></i> ERROR');
                            }
                        });
                })

                $('#resform').on({
                    'show.uk.modal': function () {
                        $('#modal-msg').html(""); //remove old messages
                    }
                });

                $('#resnow').click(function () {
                    if ($(this).prop('checked')) {
                        $('#row_resdate').hide();
                    } else {
                        $('#row_resdate').show();
                    }
                });

                $('#submitres').click(function () {
                    $.ajax({
                        method: "POST",
                        url: "index.php?option=com_lupo&task=sendres&format=raw",
                        data: {
                            clientname: $('#clientname').val(),
                            clientemail: $('#clientemail').val(),
                            clientmobile: $('#clientmobile').val(),
                            clientnr: $('#clientnr').val(),
                            resdate: ($('#resnow').prop('checked') ? 'sofort' : $('#resdate').val()),
                            comment: $('#comment').val(),
                        }
                    })
                        .done(function (msg) {
                            if (msg == 'ok') {
                                var modal = UIkit.modal("#resform");
                                modal.hide();
                                $('.lupo_loginlink_reservations').addClass('uk-hidden');
                                $('#reservations').hide().after('<div class="uk-alert uk-alert-success"><?php echo JText::_("COM_LUPO_RES_SUBMIT_SUCCESS_MSG"); ?></div>');
                            } else {
                                $('#modal-msg').html('<div class="uk-alert uk-alert-danger">' + msg + '</div>');
                            }
                        });
                });
                $('#cancelres').click(function () {
                    UIkit.modal("#resform").hide();
                })
            })
        </script>


        <div id="resform" class="uk-modal">
            <div class="uk-modal-dialog" style="background: #ffffff none repeat scroll 0 0 !important;">
                <button class="uk-modal-close uk-close" type="button"></button>
                <div class="uk-modal-header"><h2><?php echo JText::_("COM_LUPO_RES_TOYS"); ?></h2></div>
                <style>
                    .res-table {
                        width: 600px;
                    }

                    .res-table td:nth-child(1) {
                        width: 150px;
                        vertical-align: top;
                    }

                    <?php if($componentParams->get('detail_show_res_date_now', '1')==1) {?>
                    #row_resdate {
                        display: none;
                    }

                    <?php } ?>
                </style>
                <table class="res-table">
                    <tbody>
                    <tr>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_("COM_LUPO_TOY"); ?>:</td>
                        <td class="uk-text-bold">
                            <ul style="padding-left: 15px;">
								<?php
								$i = 0;
								foreach ($reservations as $reservation) {
									echo '<li id="formtoyitem' . $i++ . '">';
									echo $reservation->toyname;
									echo "</li>";
								}
								?>
                            </ul>

                        </td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_("COM_LUPO_RES_CLIENT_NO"); ?>:</td>
                        <td>
                            <input type="text" maxlength="50" size="40" value="<?= $clientnr ?>" id="clientnr" name="clientnr">
                            <span class="uk-text-muted"><?php echo JText::_("COM_LUPO_RES_CLIENT_NO_IF_AVAILABLE"); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_("COM_LUPO_RES_NAME"); ?>:*</td>
                        <td><input type="text" required maxlength="100" size="40" value="<?= $clientname ?>"
                                   id="clientname" name="clientname"></td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_("COM_LUPO_RES_EMAIL"); ?>:*<br></td>
                        <td><input type="email" required maxlength="100" size="40" value="<?= $clientemail ?>" id="clientemail" name="clientemail"></td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_("COM_LUPO_RES_MOBILE"); ?>:*<br></td>
                        <td><input type="tel" required maxlength="15" size="40" value="<?= $clientphone ?>" id="clientmobile" name="clientmobile"></td>
                    </tr>
					<?php if ($componentParams->get('detail_show_res_date', '1') == 1) { ?>
                        <tr>
                            <td><?php echo JText::_("COM_LUPO_RES_FROM"); ?>:</td>
                            <td>
                                <div style="margin-bottom: 10px">
									<?php if ($componentParams->get('detail_show_res_date_now', '1') == 1) { ?>
                                        <input type="checkbox" value="resnow" id="resnow" name="resnow" checked="checked"> <?php echo JText::_("COM_LUPO_RES_FROM_INSTANTLY"); ?>
									<?php } ?>
                                    <span class="uk-text-muted"> <?php echo JText::_("COM_LUPO_RES_FROM_INFO"); ?></span>
                                </div>
                            </td>
                        </tr>
                        <tr id="row_resdate">
                            <td></td>
                            <td>
                                <input type="text" maxlength="40" size="40" value="" id="resdate" name="resdate" placeholder="<?php echo JText::_("COM_LUPO_RES_FROM_DATE"); ?>">
                            </td>
                        </tr>
					<?php } ?>
                    <tr>
                        <td><?php echo JText::_("COM_LUPO_RES_ADDITIONAL_INFO"); ?>:</td>
                        <td><textarea rows="10" cols="70" id="comment" name="comment"
                                      style="height: 87px; width: 312px;"></textarea></td>
                    </tr>
					<?php if ($componentParams->get('detail_toy_res_costs', '') != "") { ?>
                        <tr>
                            <td><?php echo JText::_("COM_LUPO_RES_COSTS"); ?>:</td>
                            <td>
								<?= $componentParams->get('detail_toy_res_costs', ''); ?>
                            </td>
                        </tr>
					<?php } ?>
                    <tr>
                        <td></td>
                        <td>
                            <button id="cancelres" class="uk-button"><?php echo JText::_("JCANCEL"); ?></button>
                            <button id="submitres" class="uk-button uk-button-primary"><?php echo JText::_('MOD_LUPO_RESERVATIONS_SEND') ?> <i class="uk-icon-send"></i></button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="uk-modal-footer">
                    <div id="modal-msg" style="margin-top: 10px"></div>
                </div>
            </div>
        </div>
	<?php } ?>
</div>

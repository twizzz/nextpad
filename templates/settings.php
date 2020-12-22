<?php
/** @var \OCP\IL10N $l */
/** @var array $_ */

script('nextpad', 'settings');
style('nextpad', 'settings');
?>
<div class="section" id="nextpad">
    <form id="nextpad_settings">

        <h2><?php p($l->t('Nextpad (collaborative documents)'));?></h2>

        <p class="settings-hint"><?php p($l->t('This is used to link collaborative documents inside Nextcloud.')); ?></p>

        <?php if ($_['nextpad_mimetype_ep_configured'] !== 'yes' || $_['nextpad_mimetype_ec_configured'] !== 'yes') { ?>
            <p class="settings-hint"><b><?php p($l->t('Nextpad is not correctly configured, you should update your configuration. Please refer to the documentation for more information.')); ?></b></p>
        <?php } ?>

        <p>
	    <input type="checkbox" name="nextpad_etherpad_enable" id="nextpad_etherpad_enable" class="checkbox"
	           value="1" <?php if ($_['nextpad_etherpad_enable'] === 'yes') print_unescaped('checked="checked"'); ?> />
	    <label for="nextpad_etherpad_enable"><?php p($l->t('Enable Etherpad'));?></label><br/>
        </p>

        <p id="nextpad_etherpad_settings" class="indent <?php if ($_['nextpad_etherpad_enable'] !== 'yes') p('hidden'); ?>">
            <label for="nextpad_etherpad_host"><?php p($l->t('Etherpad Host')); ?></label>
            <input type="text" name="nextpad_etherpad_host" id="nextpad_etherpad_host"
	           value="<?php p($_['nextpad_etherpad_host']); ?>"
                   placeholder="https://beta.etherpad.org/" />
            <br/>

	    <input type="checkbox" name="nextpad_etherpad_useapi" id="nextpad_etherpad_useapi" class="checkbox"
	           value="1" <?php if ($_['nextpad_etherpad_useapi'] === 'yes') print_unescaped('checked="checked"'); ?> />
	    <label for="nextpad_etherpad_useapi"><?php p($l->t('Use Etherpad API (*experimental*)'));?></label><br/>

            <em>
                <?php p($l->t('You need to enable Etherpad API if you want to create “protected” pads, that will only be accessible through Nextcloud.')); ?><br/>
                <?php p($l->t('You have to host your Etherpad instance in a subdomain or sibbling domain of the one that is used by Nextcloud (due to cookie isolation).')); ?>
            </em>
        </p>

        <p id="nextpad_etherpad_useapi_settings" class="double-indent <?php if ($_['nextpad_etherpad_enable'] !== 'yes' || $_['nextpad_etherpad_useapi'] !== 'yes') p('hidden'); ?>">

            <input type="checkbox" name="nextpad_etherpad_public_enable" id="nextpad_etherpad_public_enable" class="checkbox"
                   value="1" <?php if ($_['nextpad_etherpad_public_enable'] === 'yes') print_unescaped('checked="checked"'); ?> />
            <label for="nextpad_etherpad_public_enable"><?php p($l->t('Allow “public” pads'));?></label><br/>

            <label for="nextpad_etherpad_apikey"><?php p($l->t('Etherpad Apikey')); ?></label><br/>
            <input type="text" name="nextpad_etherpad_apikey" id="nextpad_etherpad_apikey" value="<?php p($_['nextpad_etherpad_apikey']); ?>" /><br/>


            <label for="nextpad_etherpad_cookie_domain"><?php p($l->t('Etherpad cookie domain')); ?></label><br/>
            <em>
                <?php p($l->t('For example, if you host your Etherpad instance on `pad.example.org` and your Nextcloud instance on `cloud.example.org` you need to configure your cookie to `example.org` domain.')); ?>
            </em><br/>
            <input type="text" name="nextpad_etherpad_cookie_domain" id="nextpad_etherpad_cookie_domain" value="<?php p($_['nextpad_etherpad_cookie_domain']); ?>" /><br/>
        </p>

        <p>
	    <input type="checkbox" name="nextpad_ethercalc_enable" id="nextpad_ethercalc_enable" class="checkbox"
	           value="1" <?php if ($_['nextpad_ethercalc_enable'] === 'yes') print_unescaped('checked="checked"'); ?> />
	    <label for="nextpad_ethercalc_enable"><?php p($l->t('Enable Ethercalc'));?></label><br/>
        </p>

        <div id="nextpad_ethercalc_settings" class="indent <?php if ($_['nextpad_ethercalc_enable'] !== 'yes') p('hidden'); ?>">
            <p>
                <label for="nextpad_ethercalc_host"><?php p($l->t('Ethercalc Host')); ?></label>
                <input type="text" name="nextpad_ethercalc_host" id="nextpad_ethercalc_host"
	               value="<?php p($_['nextpad_ethercalc_host']); ?>"
                       placeholder="https://ethercalc.org" />
            </p>
        </div>

        <div id="nextpad-saved-message">
            <span class="msg success"><?php p($l->t('Saved')); ?></span>
        </div>
    </form>
</div>

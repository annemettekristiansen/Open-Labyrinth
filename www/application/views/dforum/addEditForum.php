<?php
/**
 * Open Labyrinth [ http://www.openlabyrinth.ca ]
 *
 * Open Labyrinth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Open Labyrinth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Open Labyrinth.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright 2012 Open Labyrinth. All Rights Reserved.
 *
 */
?>
<script language="javascript" type="text/javascript"
        src="<?php echo URL::base(); ?>scripts/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
    tinyMCE.init({
        // General options
        mode: "textareas",
        relative_urls: false,
        theme: "advanced",
        skin: "bootstrap",
        plugins: "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,imgmap",
        // Theme options
        theme_advanced_buttons1: "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons4: "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,|,imgmap",
        theme_advanced_toolbar_location: "top",
        theme_advanced_toolbar_align: "left",
        theme_advanced_statusbar_location: "bottom",
        theme_advanced_resizing: true,
        editor_selector: "mceEditor"
    });
</script>
<div class="page-header">
    <?php if (isset($templateData['name'])){ ?>
        <h1><?php echo __('Edit forum') . ' - "' .  $templateData['name'] . '"'; ?></h1>
    <?php } else { ?>
        <h1><?php echo __('Add new forum'); ?></h1>
    <?php } ?>
</div>

<form class="form-horizontal" id="form1" name="form1" method="post"
      action="<?php echo (!isset($templateData['name'])) ? URL::base() . 'dforumManager/saveNewForum/' : URL::base() . 'dforumManager/updateForum/'; ?>">

    <fieldset class="fieldset">
        <div class="control-group">
            <label for="forumname" class="control-label"><?php echo __('Forum name'); ?></label>

            <div class="controls">
                <input class="span6" type="text" name="forumname" id="forumname" value="<?php echo (isset($templateData['name'])) ? $templateData['name'] : ''; ?>" />
            </div>
        </div>

        <?php if (!isset($templateData['name'])){ ?>
        <div class="control-group">
            <label for="firstmessage" class="control-label"><?php echo __('First message'); ?></label>

            <div class="controls">
                <textarea name="firstmessage" id="firstmessage" class="mceEditor"></textarea>
            </div>
        </div>
        <?php } ?>
    </fieldset>
    <fieldset class="fieldset">
        <div class="control-group">
            <label class="control-label"><?php echo __('Security'); ?></label>

            <div class="controls">
                <label class="radio">
                    <input name="security" type="radio" value="0" <?php if (isset($templateData['security'])) { echo ($templateData['security'] == 0) ? 'checked="checked"' : ''; } else { echo 'checked="checked"'; } ?>><?php echo __('Open'); ?>
                </label>
            </div>
            <div class="controls">
                <label class="radio">
                    <input name="security" type="radio" value="1" <?php if (isset($templateData['security'])) { echo ($templateData['security'] == 1) ? 'checked="checked"' : ''; } ?>><?php echo __('Private'); ?>
                </label>

            </div>
        </div>

        <h3>Assign the users</h3>
        <table id="assign-users" class="table table-bordered table-striped">
            <colgroup>
                <col style="width: 5%" />
                <col style="width: 5%" />
                <col style="width: 90%" />
            </colgroup>
            <thead>
            <tr>
                <th style="text-align: center">Actions</th>
                <th style="text-align: center">Auth type</th>
                <th>
                    <a href="javascript:void(0);">
                        User <div class="pull-right"><i class="icon-chevron-down icon-white"></i></div>
                    </a>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php if(isset($templateData['existUsers']) and count($templateData['existUsers']) > 0) { ?>
                <?php foreach($templateData['existUsers'] as $existUser) { ?>
                    <tr>
                        <td style="text-align: center"><input type="checkbox" name="users[]" value="<?php echo $existUser['id']; ?>" checked="checked"></td>
                        <?php $icon = ($existUser['icon'] != NULL) ? 'oauth/'.$existUser['icon'] : 'openlabyrinth-header.png' ; ?>
                        <td style="text-align: center;"> <img <?php echo ($existUser['icon'] != NULL) ? 'width="32"' : ''; ?> src=" <?php echo URL::base() . 'images/' . $icon ; ?>" border="0"/></td>
                        <td><?php echo $existUser['nickname']; ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
            <?php if(isset($templateData['users']) and count($templateData['users']) > 0) { ?>
                <?php foreach($templateData['users'] as $user) { ?>
                    <tr>
                        <?php if(!isset($templateData['name']) && $user['id'] == Auth::instance()->get_user()->id) continue; ?>
                        <td style="text-align: center"><input type="checkbox" name="users[]" value="<?php echo $user['id']; ?>"></td>
                        <?php $icon = ($user['icon'] != NULL) ? 'oauth/'.$user['icon'] : 'openlabyrinth-header.png' ; ?>
                        <td style="text-align: center;"> <img <?php echo ($user['icon'] != NULL) ? 'width="32"' : ''; ?> src=" <?php echo URL::base() . 'images/' . $icon ; ?>" border="0"/></td>
                        <td><?php echo $user['nickname']; ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>

        <h3>Assign the groups</h3>
        <table id="assign-users" class="table table-bordered table-striped">
            <colgroup>
                <col style="width: 5%" />
                <col style="width: 90%" />
            </colgroup>
            <thead>
            <tr>
                <th style="text-align: center">Actions</th>
                <th>
                    <a href="javascript:void(0);">
                        Group <div class="pull-right"><i class="icon-chevron-down icon-white"></i></div>
                    </a>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php if(isset($templateData['existGroups']) and count($templateData['existGroups']) > 0) { ?>
                <?php foreach($templateData['existGroups'] as $existGroup) { ?>
                    <tr>
                        <td style="text-align: center"><input type="checkbox" name="groups[]" value="<?php echo $existGroup['id']; ?>" checked="checked"></td>
                        <td><?php echo $existGroup['name']; ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
            <?php if(isset($templateData['groups']) and count($templateData['groups']) > 0) { ?>
                <?php foreach($templateData['groups'] as $group) { ?>
                    <tr>
                        <td style="text-align: center"><input type="checkbox" name="groups[]" value="<?php echo $group->id; ?>"></td>
                        <td><?php echo $group->name; ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>
    </fieldset>
    <div class="form-actions">
        <div class="pull-right">
            <input class="btn btn-large btn-primary" type="submit" name="Submit"
                   value="<?php echo (isset($templateData['name'])) ? 'Edit forum' : 'Add forum'; ?>" onclick="return CheckForm();"></div>
        </div>
    </div>
    <?php if (isset($templateData['name'])) { ?>
    <input type="hidden" value="<?php echo $templateData['forum_id'] ?>" name="forum_id" id="forum_id" >
    <?php } ?>
</form>

<script>

    function CheckForm()
    {
        if(document.getElementById('forumname').value == '')
        {
            alert('Please enter you forum name');
            return false;
        }
        <?php if (!isset($templateData['name'])){ ?>
        if(tinyMCE.get("firstmessage").getContent() =='')
        {
            alert('Please enter you first message');
            return false;
        }
        <?php } ?>
    }

</script>

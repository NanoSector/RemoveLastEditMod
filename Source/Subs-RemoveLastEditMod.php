<?php

/* Remove "Last Edit" Mod for SMF
 *
 * @package RemoveLastEditMod
 * @author Yoshi2889
 * @version 0.4
 * 
 * @todo Keep the code working smoothly.
 */

// The actions. That needs to be done first.
function rlem_actions(&$actionArray)
{
        // Our main action, on which everything will be based.
        $actionArray['unsetedittime'] = array('Subs-RemoveLastEditMod.php', 'rlem');
}

// The permissions, allowing people to remove either their own or others' edit sign.
function rlem_permissions(&$permissionGroups, &$permissionList)
{
        // Permission groups.
        $permissionGroups['membergroup']['simple'] = array('rlem_simple');
        $permissionGroups['membergroup']['classic'] = array('rlem_classic');
        
        // Remove their own edit sign.
        $permissionList['membergroup']['rlem_do_own'] = array(false, 'rlem_classic', 'rlem_simple');
        
        // Remove others' edit sign.
        $permissionList['membergroup']['rlem_do_any'] = array(false, 'rlem_classic', 'rlem_simple');
}

// Just positions as a bridge between rlem_do and the topic view. Nothing more really.
function rlem()
{
        global $context;
        
        // Check if everything is set.
        if (empty($_REQUEST['post']))
            fatal_lang_error('remove_last_edited_error_3');
        
        // Yeah everything's set... Now do your trick.
        rlem_do((int) $_REQUEST['post']);
}

// The main function which does all the work.
// @param int $postid The ID of the post where the notice needs to be removed.
function rlem_do($postid)
{
        global $smcFunc, $scripturl, $context, $txt;
        
        // Okay, check if all required parameters are filled.
        if (!empty($postid) && !is_numeric($postid))
                fatal_lang_error('remove_last_edited_error_1');
                
        // Check if the post is valid, an ID is what it says it is, unique.
        $result = $smcFunc['db_query']('', "
                SELECT id_msg, id_topic, id_member
                FROM {db_prefix}messages
                WHERE id_msg= {int:msgid}",
                array(
                        'msgid' => $postid,
                )
        );
        
        // Does it exist?
        if ($smcFunc['db_num_rows']($result) == 0)
                fatal_lang_error('remove_last_edited_error_1');
                
        // Grab the title from the result.
        $post = $smcFunc['db_fetch_assoc']($result);
        
        // Free you go!
        $smcFunc['db_free_result']($result);
                
        // Are we allowed to do our own post, though? If we get past here we're safe.
        if (allowedTo('rlem_do_any') || (allowedTo('rlem_do_own') && $context['user']['id'] == $post['id_member']))
        {
                // This empties out the parts with which SMF determines if the post was modified, thus tricking it into believing it's not modified at all.
                $smcFunc['db_query']('', "
                        UPDATE {db_prefix}messages
                        SET
                                modified_time = 0,
                                modified_name = ''
                        WHERE id_msg = {int:msgid}",
                        array(
                                'msgid' => $postid,
                        )
                );
                
                // And we're done!
                redirectexit($scripturl . '?topic=' . $post['id_topic'] . '.msg' . $post['id_msg'] . '#msg' . $post['id_msg']);
        }
        else
                fatal_lang_error('remove_last_edited_error_2');
        
}
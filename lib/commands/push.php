<?php
function pf_push($argv) {
    system("git update-index -q --ignore-submodules --refresh");

    # Detect dirty repo
    execute("git status --porcelain", $output);
    if (!empty($output)) {
        ewrap("There are uncommited changes.");
        $commit_message = '';
        # If they don't provide a commit message, ask again and again
        while(empty($commit_message)) {
            $commit_message = prompt("Enter a commit message to check in changes: ");
        }
        die(wrap($commit_message));
        system("git add -A");
        system("git commit -m \"$commit_message\"");
    }

    # Detect git summodules
    execute("git config --list | grep '^submodule.' | wc -l", $output);
    $has_submodules = intval($output) > 0;

    if (!$has_submodules) {
        # delete local pf-deploy branch
        execute("git branch | grep 'pf-deploy' | wc -l", $output);
        if (intval($output) > 0) {
            execute("git branch -D pf-deploy");
        }

        # delete remote pf-deploy branch
        execute("git branch -r | grep 'pf-deploy' | wc -l", $output);
        if (intval($output) > 0) {
            execute("git push origin :pf-deploy", $ignore);
        }

        # push
        system("git push");
    } else {
        return pf_deploy();
    }

    return true;
}

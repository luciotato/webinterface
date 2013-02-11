<h2>Nginx Domains</h2>

<div class="floatleft">

    <fieldset style="width: 350px;">

    <legend><h3>Domains</h3></legend>

    <p>
    You might select the domains to load.
    Selecting a domain will enable it in "domain.conf".
    Deselecting a domain disables loading.
    Remember to restart Nginx for changes to take effect.
    </p>

    <form action="index.php?page=config&action=update_nginx_domains" method="post" class="well form-inline">
        <table>
            <?php
            if(!empty($domains)) {
                foreach ($domains as $domain) { /* array: fqpn, filename, loaded */

                    $checked = (isset($domain['loaded']) && $domain['loaded'] === true) ? 'checked="checked"' : '';

                    echo '<tr><td>' . $domain['filename'] . '</td><td><input type="checkbox" ' . $checked . '></td>
                          <td><a href="index.php?page=openfile&file='.$domain['filename'].'"Open in Editor</a></td></tr>';
                }
            } else {
                echo '<tr><td>No domains files found.</td></tr>';
            } ?>
        </table>
        <div class="form-actions">
            <button type="submit" class="aButton"><i class="icon-ok"></i>&nbsp;&nbsp;&nbsp;Submit</button>
            <button type="reset" class="aButton"><i class="icon-remove"></i>&nbsp;&nbsp;&nbsp;Reset</button>
        </div>
    </form>

    </fieldset>

</div>

<div class="floatright">

    <fieldset style="width: 350px;">

    <legend><h3>Add New or Edit Domain</h3></legend>

    <p>
    Please select the location (realpath) for the domain, then add the Servername.
    You might also provide aliases for the servername. Do not forget to select the checkbox
    for adding the new domain domain to your "hosts" file for local name resolution.
    </p>

    <form class="well">

        <script>
        // servername suggestion based on path
        // transfer the 'selected realpath' to the input box 'servername'
        $('#folder').click(function() {
            var selectedText = $("#folder option:selected").text().toLowerCase();
            selectedText = 'www.' + selectedText + '.dev';
            $("input[id='servername']").val(selectedText);
        });

        // add alias input field
        $('#add-alias').click(function() {
            var addAliasRow = '<tr><td><input type="text" values="aliases[]"></td><td><a id="remove-alias"><i class="icon-minus"></i>Remove Alias</a></td></tr>';
            $("table[id='aliases']").append(addAliasRow);
            $("table[id='aliases'] tr:last-child input").focus();
        });

        // remove alias input field
        $("table[id='aliases']").on("click", "a", function(event) {
            $(this).closest("tr").remove();
        });
        </script>

        <ul id="form">
          <li>
            <label class="checkbox">Location (Realpath)</label>
            <span class="block-help">Path of the project folder you want to create the domain for.</span>
            <select id="folder">
                <?php foreach ($project_folders as $folder) { ?>
                <option value="/<?=$folder?>"><?=$folder?></option>
                <?php } ?>
            </select>

            <label for="servername">Servername</label>
            <span class="block-help">Enter the servername:</span>
            <input type="text" id="servername">
            <span class="example"><b>Example:</b> LALA server</span>

            <label>Add domain to the hosts file for local name resolution?</label><input type="checkbox">

            <!--<label>(Port)</label>-->

            <!--<label>(Dynamic DNS)</label>-->

            <label>Aliases - <a id="add-alias"><i class="icon-plus"></i>Add Alias</a></label>
            <table id="aliases">
                <tr>
                    <td>Alias 1</td><td><a id="remove-alias"><i class="icon-minus"></i>Remove Alias</a></td>
                </tr>
            </table>

          </li>
        </ul>

        <div class="form-actions">
            <button type="submit" class="aButton"><i class="icon-ok"></i>&nbsp;&nbsp;&nbsp;Submit</button>
            <button type="reset" class="aButton"><i class="icon-remove"></i>&nbsp;&nbsp;&nbsp;Reset</button>
        </div>

    </form>

    </fieldset>

</div>
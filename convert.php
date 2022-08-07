<?php
error_reporting(E_ALL & ~E_NOTICE);
define('NL', "\n");

// Open export.data file (json encoded file from within the .1pux file)
$jsonFile = file_get_contents('export.data');
$jsonDecoded = json_decode($jsonFile, true);

$outVaults = array();

echo 'Accounts: ' . count($jsonDecoded['accounts']) . NL;
foreach ($jsonDecoded['accounts'] as $account) {
	echo NL;
	echo 'Account name: ' . $account['attrs']['accountName'] . NL;
	echo 'Name: ' . $account['attrs']['name'] . NL;
	echo 'E-mail: ' . $account['attrs']['email'] . NL;
	echo NL;
	echo 'Vaults: ' . count($account['vaults']) . NL;
	foreach($account['vaults'] as $vault) {
		echo NL;
		$vaultName = $vault['attrs']['name'];
		echo 'Vault name: ' . $vaultName . NL;
		echo 'Items: ' . count($vault['items']) . NL;
		$i = 0;
		foreach($vault['items'] as $item) {
			$itemTitle = $item['overview']['title'];
			$itemURL = $item['overview']['url'];
			$itemCreated = date('Y-m-d H:i:s', $item['createdAt']);
			$itemUpdated = date('Y-m-d H:i:s', $item['updatedAt']);
			$itemState = $item['state']; // active or archived

			$itemURLs = '';
			if(!empty($item['overview']['urls'])) {
				foreach($item['overview']['urls'] as $url) {
					$itemURLs = '### URLS ###' . NL;
					$itemURLs .= "Additional URL: {$url['url']} (label: {$url['label']})" . NL;
					$itemURLs .= '### END URLS ###' . NL . NL;
				}
			}

			$itemUsername = '';
			$itemPassword = '';
			foreach($item['details']['loginFields'] as $loginField) {
				if($loginField['designation'] == 'username') {
					$itemUsername = $loginField['value'];
				} elseif($loginField['designation'] == 'password') {
					$itemPassword = $loginField['value'];
				} else {
					// Autofill fields?
					//echo "OTHER: {$loginField['designation']}: {$loginField['value']} (name: {$loginField['name']}, fieldtype: {$loginField['fieldType']}) (id: {$loginField['id']})" . NL;
				}
			}

			$itemNotes = '';
			if(!empty($item['details']['notesPlain'])) {
				$itemNotes .= $item['details']['notesPlain'] . NL;
			}
			$itemSections = '';
			if(!empty($item['details']['sections'])) {
				// Contains other fields, licences, attachments, credit cards etc.
				$itemSections = '### SECTIONS ###' . NL;
				$itemSections .= print_r($item['details']['sections'], true);
				$itemSections .= '### END SECTIONS ###' . NL . NL;
			}

			$itemPwHistory = '';
			if(!empty($item['details']['passwordHistory'])) {
				$itemPwHistory = '### PASSWORD HISTORY ###' . NL;
				foreach($item['details']['passwordHistory'] as $pwHist) {
					$itemPwHistory .= 'Date: ' . date('Y-m-d H:i:s', $pwHist['time']) . ' -- PW: ' . $pwHist['value'] . NL;
				}
				$itemPwHistory .= '### END PASSWORD HISTORY ###' . NL . NL;
			}

			$itemNotes .= $itemURLs;        // Add additional URLs to notes
			$itemNotes .= $itemPwHistory;   // Add password history to notes
			$itemNotes .= $itemSections;    // Add sections (array dump, can't be bothered to parse it because it's mostly useless)

			if(!is_array($outVaults[$vaultName])) { $outVaults[$vaultName] = array(); }
			if(!is_array($outVaults[$vaultName][$itemState])) { $outVaults[$vaultName][$itemState] = array(); }
			if(!is_array($outVaults[$vaultName][$itemState][$i])) { $outVaults[$vaultName][$itemState][$i] = array(); }

			$outVaults["$vaultName"]["$itemState"]["$i"]['title'] = $itemTitle;         // Column 1
			$outVaults["$vaultName"]["$itemState"]["$i"]['username'] = $itemUsername;   // Column 2
			$outVaults["$vaultName"]["$itemState"]["$i"]['password'] = $itemPassword;   // Column 3
			$outVaults["$vaultName"]["$itemState"]["$i"]['url'] = $itemURL;             // Column 4
			$outVaults["$vaultName"]["$itemState"]["$i"]['created'] = $itemCreated;     // Column 5
			$outVaults["$vaultName"]["$itemState"]["$i"]['updated'] = $itemUpdated;     // Column 6
			$outVaults["$vaultName"]["$itemState"]["$i"]['notes'] = $itemNotes;         // Column 7
			$i++;
		}
	}
}

// Parse output array into CSV files

// vaults (private, shared, work etc)
foreach($outVaults as $vName=> $vArr) {
	// states (active or archived)
	foreach($vArr as $sName=> $sArr) {
		$vName = preg_replace("/[^a-zA-Z0-9]+/", "", $vName); // Filter vault name to safe characters
		$sName = preg_replace("/[^a-zA-Z0-9]+/", "", $sName); // Filter state, just in case

		$csvFile = "vault-{$vName}-{$sName}.csv";
		$fp = fopen($csvFile, 'w');
		echo 'Writing to file: ' . $csvFile . NL;
		echo 'Writing ' . count($sArr) . ' items to file' . NL . NL;
		foreach ($sArr as $iArr) {
			fputcsv($fp, $iArr);
		}
		fclose($fp);
	}
}
?>

<?php


$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['downloadlibrary_documentboard'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    // plugin signature: <extension key without underscores> '_' <plugin name in lowercase>
    'downloadlibrary_documentboard',
    // Flexform configuration schema file
    'FILE:EXT:download_library/Configuration/FlexForms/DocumentBoard.xml'
    );

?>

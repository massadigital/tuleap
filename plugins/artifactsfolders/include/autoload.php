<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart
// this is an autogenerated file - do not edit
function autoloadfd80b3ffcd614cc32e77cdac8111a6e2($class) {
    static $classes = null;
    if ($classes === null) {
        $classes = array(
            'artifactsfoldersplugin' => '/artifactsfoldersPlugin.class.php',
            'tuleap\\artifactsfolders\\artifactsfoldersplugindescriptor' => '/ArtifactsFoldersPluginDescriptor.php',
            'tuleap\\artifactsfolders\\artifactsfoldersplugininfo' => '/ArtifactsFoldersPluginInfo.php',
            'tuleap\\artifactsfolders\\folder\\artifactlinkinformationprepender' => '/Folder/ArtifactLinkInformationPrepender.php',
            'tuleap\\artifactsfolders\\folder\\artifactpresenter' => '/Folder/ArtifactPresenter.php',
            'tuleap\\artifactsfolders\\folder\\artifactpresenterbuilder' => '/Folder/ArtifactPresenterBuilder.php',
            'tuleap\\artifactsfolders\\folder\\artifactview' => '/Folder/ArtifactView.php',
            'tuleap\\artifactsfolders\\folder\\controller' => '/Folder/Controller.php',
            'tuleap\\artifactsfolders\\folder\\dao' => '/Folder/Dao.php',
            'tuleap\\artifactsfolders\\folder\\datafromrequestaugmentor' => '/Folder/DataFromRequestAugmentor.php',
            'tuleap\\artifactsfolders\\folder\\folderhierarchicalrepresentation' => '/Folder/FolderHierarchicalRepresentation.php',
            'tuleap\\artifactsfolders\\folder\\folderhierarchicalrepresentationcollection' => '/Folder/FolderHierarchicalRepresentationCollection.php',
            'tuleap\\artifactsfolders\\folder\\folderhierarchicalrepresentationcollectionbuilder' => '/Folder/FolderHierarchicalRepresentationCollectionBuilder.php',
            'tuleap\\artifactsfolders\\folder\\folderusageretriever' => '/Folder/FolderUsageRetriever.php',
            'tuleap\\artifactsfolders\\folder\\hierarchyoffolderbuilder' => '/Folder/HierarchyOfFolderBuilder.php',
            'tuleap\\artifactsfolders\\folder\\postsavenewchangesetcommand' => '/Folder/PostSaveNewChangesetCommand.php',
            'tuleap\\artifactsfolders\\folder\\presenter' => '/Folder/Presenter.php',
            'tuleap\\artifactsfolders\\folder\\router' => '/Folder/Router.php',
            'tuleap\\artifactsfolders\\nature\\natureinfolderpresenter' => '/Nature/NatureInFolderPresenter.php'
        );
    }
    $cn = strtolower($class);
    if (isset($classes[$cn])) {
        require dirname(__FILE__) . $classes[$cn];
    }
}
spl_autoload_register('autoloadfd80b3ffcd614cc32e77cdac8111a6e2');
// @codeCoverageIgnoreEnd

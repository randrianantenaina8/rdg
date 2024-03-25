# Custom Symfony Commands

Different Symfony commands exist to start or manage this application.

During the first installation, make
```
 php bin/console app:starter-site
```
It will :
- create a first admin user,
- create mandatories basic entities (like CGU, mentions légales...).
- create footer links (linked to previous created basic entities)
This first admin user (login: smile) is used as author for the basic entities and footer links.

If you need to create an user by cli, make

```
 php bin/console app:user [username] [email] [password]
```

username, email and password are mandatory arguments.

If you need a few data... Like 20 Actualities, use
```
 php bin/console test:fake-data
```

If some contributions have be done in pre-production, then the database will be dump to be used in production mode.
Or just, you will get a SQL dump from another environment, you will need to update the image and links URL in wysiwyg content.

First, in your .env.<environment> file, you need to update URL_SRC and URL_TARGET variables where
SRC is the domain name where the database come from (ex: recherche.preproduction.inrae.fr)
and the TARGET is the domain where you want to push your database. (ex: recherche.data.gouv.fr)

The type the following command :
```
 php bin/console app:url-migration
```

# Bundles

The project uses differents bundles to help differents functionnalities.

- easyAdmin version 3 - (https://symfony.com/bundles/EasyAdminBundle/current/index.html)  
It is used to create back-office part. All controllers created to be used with this bundles are
in namespace `App\Controller\Admin` and usually with this pattern name `[entity_name]CrudController`  
If we wish to override templates, copy-paste them in `templates/bundles/EasyAdminBundle`

- doctrineExtensions - (https://github.com/doctrine-extensions/DoctrineExtensions/tree/main/doc).  
We use timestample, annotations and sluggable parts to manage createdAt, UpdatedAt and Slug properties
automatically in content entities.

- VichUploader - (https://github.com/dustin10/VichUploaderBundle)  
We use it to manage file uploads on different entities that have a file property.

# Routing

System route:

What we name "system route", is route route like a list of entities, not route with a slug variable in it.

So, when you a new route, add it to Config::ROUTES constant.
This constant is used by app:starter-site command AND in Config > Route > add/edit form to help admin users to guess routes.

If you wish to enable an Introduction to your new routes, added it to Config::PAGE_ROUTES constant with a translation keyword.

# Custom Action on EasyAdmin
EasyAdminBundle allows you to create custom actions.
Official documentation is available here : https://symfony.com/bundles/EasyAdminBundle/3.x/actions.html

In this project, we added different custom actions :
- Visualize (published content) : to see on a new tab the published page in the Front Office (instead of find it directly in the Front Office )
- Preview (draft content) : to see what will look like our content once it will be published.
- Publish (draft content) : to copy the draft content, manually with a dedicated Service, to the published content.

## Example of custom action : Preview draft content

You need to several things to get it a preview on your content.
Like :
- Modify your CrudController to add some logic in the configureActions() method.
- Add a template in templates/front/preview that should be almost a copy past of the official template that displays the published content.
- Add an EntityDraftController in Controller/Front/Preview that should be almost (be sure it will not check published content) the same of your
EntityController:action.

Example to your EntityDraftCrudController::configureActions() with GuideDraftCrudController :
<pre><code>
public function configureActions(Actions $actions): Actions
{
    // Preview a GuideDraft on Front Office like a Guide.
    $previewOne = Action::new('previewOne', $this->translator->trans('bo.preview'))
        ->linkToRoute('front.preview.guidedraft', function (GuideDraft $draft): array {
            return [
                'slug' => ($draft->getSlug()) ? $draft->getSlug() : Config::ROUTE_ERR_PARAMS,
            ];
        })
    ->setHtmlAttributes(['target' => '_blank']);
    // ...
    $actions = parent::configureActions($actions)
        // ...
        // Disable useless action (in case of draft mode feature)
        ->disable(Action::NEW, Action::DETAIL)
        // add the custom action draft in the PAGE_INDEX (the list).
        ->add(Crud::PAGE_INDEX, $draftEdit)
        // ...
    return $actions;
}
</code></pre>

# CKEDITOR (wysiwyg)

If you wish to add an extra plugin to ckeditor,
- add the folder contains the plugin in assets/ckeditor.
- then in config/packages/fos_ckeditor.yaml, add it to plugin section as below

*example with 'contents' and 'wordcount' extra plugin.*
<pre>
fos_ck_editor:
    # extra plugins
    plugins:
        contents:
            path: "/build/ckeditor/extra-plugins/contents/"
            filename: "plugin.js"
        wordcount:
            path: "/build/ckeditor/extra-plugins/wordcount/"
            filename: "plugin.js"
</pre>

- in same config files, enable it in your specific wysiwyg configuration

*continuation of the previous example*
<pre>
main_config:
            toolbar:
                - { name: "styles", items: [ 'Wordcount', '-', 'Styles', 'Contents', 'Source' ] }
            [...]
            # Add plugins in config
            extraPlugins: 'contents, wordcount'
</pre>

- Because we use webpack Encore, add a line foreach extra plugin in the webpack.config.js file in the method addPlugin() like :

<pre>
{ from: './assets/ckeditor/contents', to: 'ckeditor/extra-plugins/contents/[path][name][ext]'}
{ from: './assets/ckeditor/wordcount', to: 'ckeditor/extra-plugins/wordcount/[path][name][ext]'}
</pre>

- Last thing : recompile all your assets with the following command : `yarn dev`

# LAMINA

Laminas, in this project, are only displayed in the  Front Office Homepage.
There are several kinds of laminas :
- Carousel (where we can only added Institution entity) that is a... carousel to display the associated entity's header picture.
- CenterMap (that display points that represents Institution and/or Dataworkshop.)
- News (that display up to 5 Actuality and 5 Event)
- Spotlight (that display a Dataset)
- Highlighted (that display several Dataset)

If you want to create a new kind of lamina, you have to :
- Extends the Abstract Class Lame that is in Entity/Lame/Lame.php
- Be sure you created your lamina in the same namespace App/Entity/Lame.
- Create your translated class. All laminas have, at least, a 'title' property (that is a translated property).
- Put all your associated Repository class in Repository/Lame.
- Do not forget to add the custom constraint on the class "LaminaConstraint"
- Update the Abstract Class constant `Lame::TYPE` where the key is the translation key that represent your Entity's name in CRUD
and the value is the name class.
- Create your CrudController `php bin/console make:admin:crud` Then almost copy/paste the logic from another LaminaCrudController.
- Update the LaminaService with yours. Do not hesitate to take a look with another lame.
  - Add your class in the constant LaminaService::TYPES.
  - Update the method `getLaminaData()`
- Add your FO template and name it with the same convention and the same directory than other laminas.

# CODE QUALITY

Checking for RDG code quality is mandatory. 
Tools to reach the goal of RDG Continous Integration without a DevOps perspective are available in this project.

## Php syntax rules (PSR12)
Look for syntax errors and bugs through the application by installing the following components:
- PHPCS (official documentation: https://marketplace.visualstudio.com/items?itemName=ikappas.phpcs) 
- PHPStan (official documentation: https://phpstan.org/user-guide/getting-started)
- PHP Code Beautifier and Fixer (official documentation: https://phpqa.io/projects/phpcbf.html)

Once installed the above components, in order to find for PSR12 syntax errors, lauch the below command from /symfony folder:
<pre>
vendor/bin/phpcs -v --standard=PSR12 --ignore=./src/Kernel.php ./src --warning-severity=0
</pre>
- If you also want to check for deprecation warnings, enable the --warning-severity flag as follow: 
<pre>
vendor/bin/phpcs -v --standard=PSR12 --ignore=./src/Kernel.php ./src --warning-severity=1
</pre>
- phpcs component will find PSR12 errors for you. Look at the output and launch the following command to automatically fix errors :
<pre>
vendor/bin/phpcbf --ignore-annotations
</pre>
- Sometime few errors could remain, so you have to fix them manually.  

## Php bugs sniffer
- In order to find bugs before they reach remote environment, launch the command below : 
<pre>
vendor/bin/phpstan analyse -l 0 src tests
</pre>
- If phpstan finds errors, they will be displayed by file, line and a detailed description. It is mandatory to fix them (**otherwise, CI/CD pipeline will fail**).
For ex., bugs sniffed by this component could be:
- Undefined properties
- Not found classes
- Invalid typehint
- Access to undefined properties and so on.

## Twig linter
- Check Twig front-office files by launching the following command:
<pre>
php bin/console lint:twig templates/ --show-deprecations
</pre>
The output will show you I twig files contain syntax errors. In that case, you have to fix them manually into the /templates folder.

## Yaml linter
- Check Yaml files syntax by launching the command below:
<pre>
php bin/console lint:yaml ./config
</pre>

# CODE QUALITY TESTS

Running RDG Code quality Tests is mandatory.
RDG code quality tests are organized into two groups: Unit tests and Functional tests.
This tests categories generate a coverage report and a log journal.

## Requirements
In order to run Unit tests, you need to install Xdebug, PhpUnit packages of Sebastian Bergmann and Fabien Potencier's Symfony packages on your local environment.

To do this, from the **rdg-portal** folder, launch composer installation as following:
<pre>
make shell-php
</pre>
Then, you must launch the composer following command as root: 
<pre>
composer require phpunit/phpunit:9.6
composer require phpunit/php-code-coverage:9.2
composer require symfony/phpunit-bridge:5.3
</pre>
Also you can find this packages on the packagist website:
- https://packagist.org/packages/phpunit/phpunit
- https://packagist.org/packages/phpunit/php-code-coverage
- https://packagist.org/packages/symfony/phpunit-bridge

### Xdebug
In order to generate a code coverage report, you need to install the Xdebug component in your environment (official site https://xdebug.org/).

For WSL2 Ubuntu 20.04 TLS environment, you can follow this documentation

- https://www.cyberithub.com/how-to-install-php-xdebug-on-ubuntu-20-04-lts-focal-fossa/

- In order to setup xdebug within a Docker environment, follow this example:
https://www.garygitton.fr/setup-xdebug-php-docker/   

**Warning**
- If Xdebug component is not installed nor enabled in your environment, you can't generate any code coverage report.
Take a look of this documentation for further explainations https://www.lambdatest.com/blog/phpunit-code-coverage-report-html/  

### Phpunit.xml configuration
Once the mandatory packages installed, check the **phpunit.xml.dist** file in the project root.
You must duplicate this file and **remove .dist** extension for a local copy.

**Warning**
- **phpunit.xml.dist has been set to match with PHPUnit 9.6 writing rules**.

## Writing PHPUnit Tests
PHPunit tests writing rules and samples are illustrated into PHPUnit Documentation (9.6 version):
-  https://docs.phpunit.de/en/9.6/

PHPUnit tests are highly recommended to test the project Entities.

**Reading Assertions, Code coverage analysis and Annotations chapters is highly recommended.**

All the RDG Unit tests have to be write into the **tests/unit folder**.
All the files inserted in this folder contain the group annotation:
<pre>
/**
 * @group Unit 
 */
</pre>


## Writing Symfony Functional Tests
Symfony functional tests are illustrated into Symfony 5.4 official documentation:
-  https://symfony.com/doc/5.4/the-fast-track/en/17-tests.html#writing-functional-tests-for-controllers

Functional tests are highly recommended for Symfony Controllers and Services features.

All the RDG functional tests have to be write into the **tests/application folder**.
All the files inserted in this folder contain the group annotation:
<pre>
/**
 * @group Application 
 */
</pre>

## Launch PHPUnit Tests
PHPUnit tests must be executed from the **/rdg-portal/symfony** folder.
In order to execute unit tests, launch the following command:
<pre>
./vendor/bin/phpunit --group Unit --debug
</pre>
-- debug flag will help you easily find mistakes.

Launching this command will generate the RDG PHPUnit tests code coverage.
3 subfolders will appear into **/tests** folder:

- **cache**: contains unit tests cache
- **logs**: generates a coverage.html file with the tests list
- **report**: generates all the project src directory with the unit tests report dashboard 

You also can disable automated report generation int phpunit.xml file and launch the following command in order to read code coverage results directly into your terminal.
<pre>
./vendor/bin/phpunit --group Unit --coverage-text --debug
</pre>

## Launch Functional Tests
Functional tests must be executed from the **/rdg-portal** folder, as root user.
In order to execute function tests, launch the following commands:
<pre>
make shell-php
./vendor/bin/phpunit --group Application --debug
</pre>
-- debug flag will help you easily find mistakes.

**Note**:
Functional tests do not generate code coverage.

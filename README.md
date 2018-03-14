# SilverStripe SSCounter Field Module

## Overview

SSCounter is simple sprinkle of React and Redux to a normal implementation of `TextField`.
This is an exercise about learning and creating a SilverStripe 4.x module with React and Redux on top of it.
Improvements will be based on the Author's progress with the said technologies.
Feel free to learn from it but don't use it as a single source of truth. :)

## Installation

```
$ composer require silverstripe/marcz-sscounter
```

You'll also need to run `vendor/bin/sake dev/build`.

## Concepts

1. Like `TextField` which we can create as many as we like. `SSCounterField` will behave similar to it by making small React components which are independent of each other, and will manage its own local state for the value.
2. Implement **Redux** to tap into the Frameworks's global state for storing information of all small components created. Probably tap into "Redux Time Travel".

## Todos

1. Unit testing React components with **Jest**
2. More refactoring from peer review.
3. GraphQL maybe, REST for the meantime.

## Documentation

So this is an implementaion of `TextField`, and to illustrate the idea.
We will create a simple Data Object called `SampleCounter`

File: `mysite/code/Model/SampleCounter.php`

```
<?php
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextField;

class SampleCounter extends DataObject
{
    private static $db = [
        'Name'           => 'Varchar',
        'InfoCounter'    => 'Int',
        'SuccessCounter' => 'Int',
        'WarningCounter' => 'Int',
        'DangerCounter'  => 'Int',
        'HappyCounter'   => 'Int',
        'SadCounter'     => 'Int',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main', [
            TextField::create('InfoCounter', 'Info Count', 0)->addExtraClass('sscounter-info'),
            TextField::create('SuccessCounter', 'Success Count', 1)->addExtraClass('sscounter-success'),
            TextField::create('WarningCounter', 'Warning Count', 2)->addExtraClass('sscounter-warning'),
            TextField::create('DangerCounter', 'Danger Count', 3)->addExtraClass('sscounter-danger')
        ]);

        $fields->addFieldsToTab('Root.HappySad', [
            TextField::create('HappyCounter', 'Happy Count', 4)->addExtraClass('sscounter-happy'),
            TextField::create('SadCounter', 'Sad Count', 5)->addExtraClass('sscounter-sad')
        ]);

        return $fields;
    }
}
```

And then we will need a `ModelAdmin` in order to create some records.

File: `mysite/code/Comntrollers/AdminCounter.php`

```
<?php
use SilverStripe\Admin\ModelAdmin;

class AdminCounter extends ModelAdmin
{
    private static $managed_models = [
        'SampleCounter',
    ];

    private static $url_segment = 'counters';

    private static $menu_title = 'Counters';
}
```

Pretty much so far this is very straight forward. We now can reload the page with `?flush`
Below is the screenshot of a single record we can create and edit.

![TextField Version](https://raw.githubusercontent.com/marczhermo/silverstripe-sscounter/master/docs/img/TextField_version.png)

Now, all we need to do is change `TextField` into `SSCounterField` to the Data Object file.
Don't forget to update the `use` statement above to reference the namespace.
The edited file should like below:

```
<?php
use SilverStripe\ORM\DataObject;
use Marcz\SSCounter\SSCounterField;

class SampleCounter extends DataObject
{
    private static $db = [
        'Name'           => 'Varchar',
        'InfoCounter'    => 'Int',
        'SuccessCounter' => 'Int',
        'WarningCounter' => 'Int',
        'DangerCounter'  => 'Int',
        'HappyCounter'   => 'Int',
        'SadCounter'     => 'Int',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main', [
            SSCounterField::create('InfoCounter', 'Info Count', 0)->addExtraClass('sscounter-info'),
            SSCounterField::create('SuccessCounter', 'Success Count', 1)->addExtraClass('sscounter-success'),
            SSCounterField::create('WarningCounter', 'Warning Count', 2)->addExtraClass('sscounter-warning'),
            SSCounterField::create('DangerCounter', 'Danger Count', 3)->addExtraClass('sscounter-danger')
        ]);

        $fields->addFieldsToTab('Root.HappySad', [
            SSCounterField::create('HappyCounter', 'Happy Count', 4)->addExtraClass('sscounter-happy'),
            SSCounterField::create('SadCounter', 'Sad Count', 5)->addExtraClass('sscounter-sad')
        ]);

        return $fields;
    }
}
```

And hopefully, with any luck we should see the following screenshot when whe refreshed the page. Fingers crossed.
![SSCounterField Version](https://raw.githubusercontent.com/marczhermo/silverstripe-sscounter/master/docs/img/SSCounterField_version.png)


## Versioning

This library follows [Semver](http://semver.org). According to Semver,
you will be able to upgrade to any minor or patch version of this library
without any breaking changes to the public API. Semver also requires that
we clearly define the public API for this library.

All methods, with `public` visibility, are part of the public API. All
other methods are not part of the public API. Where possible, we'll try
to keep `protected` methods backwards-compatible in minor/patch versions,
but if you're overriding methods then please test your work before upgrading.

## Reporting Issues

Please [create an issue](https://github.com/marczhermo/silverstripe-sscounter/issues)
for any bugs you've found, or features you're missing.

## License

This module is released under the [BSD 3-Clause License](LICENSE)

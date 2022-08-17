![Andante Project Logo](https://github.com/andanteproject/soft-deletable-bundle/blob/main/andanteproject-logo.png?raw=true)
# Soft Deletable Bundle 
#### Symfony Bundle - [AndanteProject](https://github.com/andanteproject)
[![Latest Version](https://img.shields.io/github/release/andanteproject/soft-deletable-bundle.svg)](https://github.com/andanteproject/soft-deletable-bundle/releases)
![Github actions](https://github.com/andanteproject/soft-deletable-bundle/actions/workflows/workflow.yml/badge.svg?branch=main)
![Framework](https://img.shields.io/badge/Symfony-4.x|5.x-informational?Style=flat&logo=symfony)
![Php7](https://img.shields.io/badge/PHP-%207.4|8.x-informational?style=flat&logo=php)
![PhpStan](https://img.shields.io/badge/PHPStan-Level%208-syccess?style=flat&logo=php) 

Simple Symfony Bundle to handle [soft delete](https://en.wiktionary.org/wiki/soft_deletion) for doctrine entities. So your entities "_are not going to be deleted for real from the database_". 🙌 

## Requirements
Symfony 4.x-5.x and PHP 7.4.

## Install
Via [Composer](https://getcomposer.org/):
```bash
$ composer require andanteproject/soft-deletable-bundle
```

## Features
- No configuration required to be ready to go but fully customizabile;
- `deleteAt` property is as a `?\DateTimeImmutable`;
- You can disable the filter runtime even for just some entities; 
- No annotation required;
- Works like magic ✨.

## Basic usage
After [install](#install), make sure you have the bundle registered in your symfony bundles list (`config/bundles.php`):
```php
return [
    /// bundles...
    Andante\SoftDeletableBundle\AndanteSoftDeletableBundle::class => ['all' => true],
    /// bundles...
];
```
This should have been done automagically if you are using [Symfony Flex](https://flex.symfony.com). Otherwise, just register it by yourself.

Let's suppose we have a `App\Entity\Article` doctrine entity we want to enable to soft-deletion.
All you have to do is to implement `Andante\SoftDeletableBundle\SoftDeletable\SoftDeletableInterface` and use `Andante\SoftDeletableBundle\SoftDeletable\SoftDeletableTrait` trait.

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Andante\SoftDeletableBundle\SoftDeletable\SoftDeletableInterface;
use Andante\SoftDeletableBundle\SoftDeletable\SoftDeletableTrait;

/**
 * @ORM\Entity()
 */
class Article implements SoftDeletableInterface // <-- implement this
{
    use SoftDeletableTrait; // <-- add this

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string")
     */
    private string $title;
    
    public function __construct(string $title)
    {
        $this->title = $title;
    }
    
    // ...
    // Some others beautiful properties and methods ...
    // ...
}
```
Make sure to update you database schema following your doctrine workflow (`bin/console doctrine:schema:update --force` if you are a badass devil guy or with a [migration](https://www.doctrine-project.org/projects/doctrine-migrations/en/3.0/reference/introduction.html) if you choosed the be a better developer!).

You shoud see a new column named `deleted_at` ([can i change this?](#configuration-completely-optional)) or something similar based on your [doctrine naming strategy](https://www.doctrine-project.org/projects/doctrine-orm/en/2.8/reference/namingstrategy.html). 

#### Congrats! You're done! 🎉
From now on, when you delete your entity, it will be not hard-deleted from the database.
For example, let's suppose to save a new `Article`:
```php
$article = new Article('Free 🍕 for everyone!');
$entityManager->persist($article);
$entityManager->flush();
```
And so we will have it on our database.

| id | title | ... | deleted_at | 
| --- | --- | --- | --- |
| 1 | Free 🍕 for everyone! | ... | `NULL` |

But, if you delete it with Doctrine, the row will still be there but with the `deleted_at` populated with the date of its delation.

```php
$entityManager->remove($article);
$entityManager->flush();    
```
| id | title | ... | deleted_at | 
| --- | --- | --- | --- |
| 1 | Free 🍕 for everyone! | ... | `2021-01-01 10:30:00` |

And the **entity will be no more available** from your app queries. ([Is there a way I can restore them?](#disabling-soft-delete-filter))

```php
$articleArrayWithNoFreePizza = $entityManager->getRepsitory(Article::class)->findAll();
//Every entity with a deleted_at date is going to be ignored from your queries
```

## Gosh, what are you  doing to my poor entities?! 🤭
No entity was mistreated while using this bundle 🙌.

We suggest you to use `Andante\SoftDeletableBundle\SoftDeletable\SoftDeletableTrait` trait to make your life easier. It does nothing special under the hood:
it adds a `\DateTimeImmutable deletedAt` property to your entity mapped with our `deleted_at` **doctrine type** and a getter/setter to handle it.

But, for whatever reason, you are free to do it yourself (implementing `SoftDeletableInterface` is mandatory instead).

## Usage with no trait
```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Andante\SoftDeletableBundle\SoftDeletable\SoftDeletableInterface;

/**
 * @ORM\Entity()
 */
class Article implements SoftDeletableInterface // <-- implement this
{
    // No trait needed
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string")
     */
    private string $title;
    
    // DO NOT use ORM annotations to map this property. See bundle configuration section for more info 
    private ?\DateTimeImmutable $deletedAt = null; 
    
    public function __construct(string $title)
    {
        $this->title = $title;
    }
    
    public function getDeletedAt() : ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(\DateTimeImmutable $deletedAt = null) : void
    {
        $this->deletedAt = $deletedAt;
    }
}
```
This allows you to, for instance, to have **a different name** for your property (E.g. `deleted` instead of `deletedAt`).
But you will need to explicit this in [bundle configuration](#configuration-completely-optional).

## Disabling soft-delete filter
You can disable the filter entirely runtime by doing this to your Entity Manager.
```php
use Andante\SoftDeletableBundle\Doctrine\Filter\SoftDeletableFilter;
/** @var $entityManager \Doctrine\ORM\EntityManagerInterface */
$entityManager->getFilters()->disable(SoftDeletableFilter::NAME);
// From now on, entities with a "deletedAt" date are again available.
// If you want to enable the filter back:
$entityManager->getFilters()->enable(SoftDeletableFilter::NAME);
```
If you want you can also disable the filter for just one or more entities by doing this:
```php
/** @var $softDeletableFilter Andante\SoftDeletableBundle\Doctrine\Filter\SoftDeletableFilter */
$softDeletableFilter = $entityManager->getFilters()->getFilter(SoftDeletableFilter::NAME);
$softDeletableFilter->disableForEntity(Article::class);
// From now on, filter is still on but disabled just for Articles
$softDeletableFilter->enableForEntity(Article::class);
```
## Configuration (completely optional)
This bundle is build thinking how to save you time and follow best practices as close as possible.

This means you can even ignore to have a `andante_soft_deletable.yaml` config file in your application.

However, for whatever reason (legacy code?), use the bundle configuration to change most of the behaviors as your needs.
```yaml
andante_soft_deletable:
  deleted_date_aware: true # default: true
                           # Set the filter to also check deleted date value.
                           # If set true, Future date will still be avaiable 
  default:
    property_name: deletedAt # default: deletedAt
                             # The property to be used by default as deletedAt date 
                             # inside entities implementing SoftDeletableInterface
    
    column_name: deleted_at # default: null
                           # Column name to be used on database. 
                           # If set to NULL will use your default doctrine naming strategy
    table_index: false # default: true
                       # Adds automatically a table index to deleted date column
    
    always_update_deleted_at: true # default: false
                                   # if set to true, when you delete an entity which has already
                                   # a deleted date, the date will be updated to last deletion.
  entity: # You can use per-entity configuration to override default config
    Andante\SoftDeletableBundle\Tests\Fixtures\Entity\Organization:
      property_name: deletedAt
      table_index: true
    Andante\SoftDeletableBundle\Tests\Fixtures\Entity\Address:
      property_name: deleted
      column_name: delete_date
      table_index: false
      always_update_deleted_at: false
```

## Please note
- This bundle does not handle direct [DQL](https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/dql-doctrine-query-language.html) queries;
- The default setting of `deleted_date_aware` is `false`. The filter is going to exclude whatever row with a `NOT NULL` deleted date. If you want to exclude only rows with a `deletedAt` date in the past and still retrieving the ones with future dates, you need to set `deleted_date_aware` to `true`. 

Built with love ❤️ by [AndanteProject](https://github.com/andanteproject) team.

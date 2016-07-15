# SfsAdmin Documentation

Administration bundle for Symfony2

---

## Overview
SfsAdmin is an entity-oriented back-office generator. It allows you to generate fastly a CRUD system for your whole website.

- Deploy easily your CRUD system for every entities of your project, with few configuration
- Doctrine ORM oriented only
- Usage of native Symfony2 FormTypes for your create/update pages
- Filter mechanisms inside the listing pages: [LexikFormFilterBundle](https://github.com/lexik/LexikFormFilterBundle) does a great job for it, so it is natively supported
- Export system in JSON & CSV
- Add custom pages easily
- Only few dependencies

<img src="/img/overview.jpg" style="width:100%;" />

---
## Installation

Inside your current project, execute the following command to download the bundle:
```
$ composer require sfs/admin-bundle "dev-master"
```
This command requires you to have Composer installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

---
## Configuration

###Configure the kernel
To enable the bundle, you need to add the following lines in the *app/AppKernel.php* file:
```
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
			new Knp\Bundle\MenuBundle\KnpMenuBundle(),
			new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
			new Lexik\Bundle\FormFilterBundle\LexikFormFilterBundle(),
			new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),

			new Sfs\AdminBundle\SfsAdminBundle(),
        );

        // ...
    }

    // ...
}
```

###Config file
Now configure the bundle in no time, by creating a file named *sfs_admin.yml* in your *app/config* folder:
```
sfs_admin:
	title_logo: 
	title_text: SfsAdmin
    menu_categories:
        - { name: Content, icon: fa-folder }
```
As you can see you can display an asset as your logo using *title_logo* configuration, instead of simple text.
You can configure as much as categories you need, to be displayed on the side menu. Your categories will contain one entry for each admin resource.

Import this file in *app/config/config.yml*:
```
imports:
    ...
    - { resource: sfs_admin.yml }
```

###Install assets
To display the administration correctly you need to install the assets : SfsAdmin relies on Bootstrap3, and a few other jQuery plugins to create a nice user-friendly interface.

In your project folder, enter the following command:
```
php app/console assets:install
```

###Import routing system
SfsAdmin generates routes on the fly every time you create a new admin resource. Thoses routes corresponds to :

- List
- Create
- Read
- Update
- Delete
- Export

To enable this functionality, you need to add those lines inside your *app/config/routing.yml* file:
```
sfs_admin:
    resource: "@SfsAdminBundle/Resources/config/routing.yml"
    prefix: /admin
sfs_admin_resources:
    resource: .
    type: adminRoute
    prefix: /admin
```


###Security
SfsAdmin requires no specific user bundle, so that you can run your personal one or the commonly used FOSUser. The login page is setted in the *Controller/SecurityController* class, and can easily be overriden.
The following is only a functional example of *app/config/security.yml*, to show you how to configurate your administration. The login and logout pages must be anonymously accessible.

Please refer to the [FOSUserBundle documentation](https://symfony.com/doc/1.3.x/bundles/FOSUserBundle/index.html) for advanced configuration.
```
security:
    encoders:
        FOS\UserBundle\Model\UserInterface:
            algorithm: bcrypt
            cost: 13

    providers:
        fos_userbundle:
            id: fos_user.user_manager

	firewalls:
        adminstration:
            pattern:            /admin/(.*)
            switch_user:        true
            context:            user
            form_login:
                login_path:     sfs_admin_login
                use_forward:    false
                check_path:     sfs_admin_login
                failure_path:   null
                default_target_path: sfs_admin_dashboard
            logout:
                path:   sfs_admin_logout
                target: sfs_admin_login
                invalidate_session: false
            remember_me:
                secret:   '%secret%'
                lifetime: 604800
                path:     /admin
            anonymous:          true

    access_control:
        ...
        # URL of admin which need to be available to anonymous users
        - { path: ^/admin/login, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/admin/login-check, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/admin/logout, role: IS_AUTHENTICATED_ANONYMOUSLY }
        # Secured part of the site
        # This config requires being logged for the whole site and having the admin role for the admin part.
        # Change these rules to adapt them to your needs
        - { path: ^/admin/, role: ROLE_ADMIN }
```

You can read now the [Common Usage](common-usage.md) page to create your very first Admin Resource.

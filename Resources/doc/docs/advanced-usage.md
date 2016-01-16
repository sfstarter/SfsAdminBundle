# Advanced Usage
Useful implemented functionalities and knowledge of SfsAdmin

---
## Topbar mechanisms
The bundle integrates two simple ways to develop and integrate your own buttons & blocks inside the topbar menu. The first and easier is to edit your config file:
```
sfs_admin:
...
    topbar_buttons:
        - { title: Google, icon: fa-google, url: http://google.com }
```
The array *topbar_buttons* requires three parameters: the *title* of the button, a font-awesome *icon* and a *url* OR a symfony2 *route*. That way you will immediately see that a button Google appeared in the topbar.
<figure>
	<img src="/img/topbar_buttons.jpg" style="display: block; margin: auto;" />
	<figcaption style="text-align: center; font-size: 12px;">Displaying three buttons in topbar menu, aside of the user block</figcaption>
</figure>

But you might want a more complex block : to display a dropdown menu for example with multiple buttons. In that case let's create a new service :
```
services:
    sfs.admin.topbar_menu.user:
        class: Sfs\AdminBundle\Menu\Topbar\UserBlock
        arguments:
            - @twig
        tags:
            - { name: sfs_admin.menu.topbar }
```
Tag it with *sfs_admin.menu.topbar*, so that SfsAdminBundle will recognize it as a block to be displayed in the topbar. The *UserBlock* class must implements *Sfs\AdminBundle\Menu\Topbar\TopbarBlockInterface*.
```
<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Menu\Topbar;

class UserBlock extends TopbarBlockAbstract
{
	private $twig;

	public function __construct($twig) {
		$this->twig = $twig;
	}

	public function display() {
		return $this->twig->render('SfsAdminBundle:Menu/TopbarBlocks:user.html.twig', array());
	}
}
```
Now you are free to render anything you want, using the *display* method, to return a twig file.

The *UserBlock* class is actually a true working code, already implemented so you should see it immediately after the installation of SfsAdminBundle.

---
## Create pages
To be written

---
## Architecture
To be written

---
## Complete Configuration
```
sfs_admin:
	title_logo: 
	title_text: SfsAdmin
    menu_categories: [{ name, icon }]
    pages: [{ title, route, slug, category, icon }]
    topbar_buttons: [{ title, route, url, icon }]
```

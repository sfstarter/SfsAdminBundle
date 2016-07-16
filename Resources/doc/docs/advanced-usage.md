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
The array *topbar_buttons* requires three parameters: the *title* of the button, a font-awesome *icon* and a *url* OR a Symfony3 *route*. That way you will immediately see that a button Google appeared in the topbar.
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
 * SfsAdminBundle - Symfony3 project
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
## Charts
SfsAdmin uses [Chart.js](http://www.chartjs.org/) to display charts. The bundle automatically interfaces your datas to the jQuery plugin.
Two types of charts are available: graphs, and pies.

### Graphs
Three classes generate different styles of graphs, by extending *SfsAdminBundle/Chart/ChartAbstract*:

- *Sfs\AdminBundle\Chart\ChartLine*
- *Sfs\AdminBundle\Chart\ChartBar*
- *Sfs\AdminBundle\Chart\ChartRadar*

Simple working example:
```
<?php

use SfsAdminBundle\Chart\ChartLine;
...

$chart = new ChartLine($this->get('twig'));
$labels = array('January', 'February', 'March');
$chart->setLabels($labels);

$chart->addRowData('Goals', array('100', '150', '200'));

return new Response($chart->render());
```

The *Charts* objects all implements *Sfs\AdminBundle\Chart\ChartInterface*, which renders a Twig result.

Two steps only to create a simple graph:

- call first *setLabels*, which receive an array of strings to be displayed in the X axis.
- call *addRowData*, which receives a label related to the curve, an array of datas (should have the same length than the labels array), a *rgb* array to define the inside color of the curve, a *rgb* array for the stroke, and a *rgb* array for the points.

### Pies
Three classes generate different styles of pies, by extending *SfsAdminBundle/Chart/PieAbstract*:

- *Sfs\AdminBundle\Chart\ChartPie*
- *Sfs\AdminBundle\Chart\ChartPolar*
- *Sfs\AdminBundle\Chart\ChartDoughnut*

Simple working example:
```
<?php

use SfsAdminBundle\Chart\ChartLine;
...

$chart = new ChartPie($this->get('twig'));

$chart->addRowData('Microsoft', 60, array('200', '120', '100'));
$chart->addRowData('Google', 40, array('100', '150', '200'));

return new Response($chart->render());
```
To display a pie, all you have to do is to call as much as you have datas the function *addRowData*.
It takes first a label, a value, an *rgb* array for the color, and a *rgb* array for the highlighted color.


Read the [documentation of Chart.js](http://www.chartjs.org/docs/) for more informations.

---
## Create pages
Since you set correctly your firewall and refuse access on */admin* (or any other path you setted for you administration) if the user is not logged in, you can create and add any page you want to your back-office.
To do so, create a Controller and set it's path prefix to */admin*.
The template used in your returned response shall look like so:

```
{% extends 'SfsAdminBundle:Core:base.html.twig' %}

{% block content %}
...
{% endblock %}
```
Complete the *content block* as you wish, and you will benefit all of the UI from SfsAdminBundle. To add links to this page use the topbar mechanisms, or override the MenuBuilder.

A good example of this is the default dashboardAction from *Sfs\AdminBundle\Controller\PageController*, that you definitely should override to create an awesome dashboard page.

---
## Architecture
SfsAdmin is composed of a few main classes:

*Sfs\AdminBundle\DependencyInjection\Compiler\CompilerPass* searches all the services tagged as *sfs_admin.resource* to register them in the *Sfs\AdminBundle\Core\CoreAdmin*'s $admins array.

It creates dynamically the routes in an array, for each resources created. This *CoreAdmin* class keeps track of each routes and entities related to the admin resource.
It allows you to access to any of your object directy from it through its' methods.

The routes are finally generated from the *CoreAdmin* using the *Sfs\AdminBundle\Routing\RouteAdminLoader* (service *sfs.admin.routing_loader*), called automatically from Symfony because of the tag *routing.loader*.

The *Sfs\AdminBundle\Listener\AdminListener* catches each time a controller is called, so that we know what is the current admin resource. This current admin and the current action are setted in *CoreAdmin*.


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

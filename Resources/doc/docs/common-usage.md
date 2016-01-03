# Common Usage
Integrate SfsAdmin in your own Symfony2 webiste

---
##Create an admin entity
Considering you have an entity *Project\DemoBundle\Entity\Page* you want to manage. You need to create an Admin class, called in the core of SfsAdmin as Admin Resource. This class will be related to a specific entity, and it will help you to override functionalities applied to it.
```
<?php

namespace Project\DemoBundle\Admin;

use Sfs\AdminBundle\Controller\AdminController;

class PageAdmin extends AdminController
{
	protected $resourceName = "Page";

	public function setListFields() {
		return array(
				'id' 				=> array('name' => 'ID'),
				'title'				=> array('name' => 'Title'),
				'slug'				=> array('name' => 'Slug'),
				'updatedAt'			=> array('name' => 'Update Date'),
		);
	}

	protected function setUpdateForm($object) {
		$updateForm = $this->createAdminForm(new \Project\DemoBundle\Admin\Form\Type\PageType(), $object);

		return $updateForm;
	}
}
```
The AdminController is an abstract class, and requires you to define which form to apply on create / update. Use the method *createAdminForm*, as it uses its' own form factory giving you some new functionalities.


Now inside your *app/config/services.yml* file, create a service for the new admin resource:
```
services:
    ...
    sfs_admin.page:
        class: Project\DemoBundle\Admin\PageAdmin
        tags:
            - { name: sfs_admin.resource, title: "Pages", slug: "page", category: "Content", icon: "fa-file-o" }
        arguments:
            - Project\DemoBundle\Entity\Page
```
The core of SfsAdmin will take into account every services tagged with *sfs_admin.resource*.

The category value corresponds to the *menu_categories* written in *app/config/sfs_admin.yml*, using the variable *name*. The icon variable is not required.

The service takes only one argument: your associated entity.

###Create & Update forms
You can have different forms for the create page and the update one. Just override the method *setCreateForm($object)*, as by default it inherits the *setUpdateForm($object)*.

```
<?php

namespace Project\DemoBundle\Admin\Form\Type;

use Sfs\AdminBundle\Form\AbstractAdminType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PageType extends AbstractAdminType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->addTab("General")
				->addBlock("Content", array('column' => 'col-md-12'))
					->add('title')
					->add('content', null, array(
						'attr' => array('class' => 'wysihtml5', 'rows' => 6)
					))
				->endBlock()
			->endTab()
		;
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Project\DemoBundle\Entity\Page',
		));
	}

	public function getName()
	{
		return 'admin_page';
	}
}
```
Build your form just like you would do in Symfony2, with one exception: use the methods *addTab()*, *endTab()*, *addBlock()*, *endBlock()* to contain your fields, as much as you want. It will help the engine to properly design the form.

---
##Filter form
SfsAdmin supports natively the bundle [LexikFormFilterBundle](https://github.com/lexik/LexikFormFilterBundle) to generate filters. By default none will appear, as you need to create your own filter forms.

In your Admin Resource, you need to set the filter form class:
```
	public function setFilterForm() {
		$this->filterForm = $this->createForm(new \Project\DemoBundle\Admin\Form\Filter\PageFilterType());
	}
```

Now for your form:
```
<?php

namespace Project\DemoBundle\Admin\Form\Filter;

use Symfony\Component\Form\FormBuilderInterface;
use Sfs\AdminBundle\Form\AbstractFilterType;

class TuneFilterType extends AbstractFilterType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('id', 'filter_number');
		$builder->add('title', 'filter_text');
		$builder->add('enabled', 'sfs_admin_filter_boolean');
		$builder->add('updatedAt', 'sfs_admin_filter_datetime_range', array(
			'left_datetime_options' => array(
					'widget' => 'single_text',
					'format' => 'dd/MM/yyyy',
					'label' => 'From',
					'attr' => array(
							'data-date-locale' => 'fr',
							'data-date-format' => 'DD/MM/YYYY'
					)
			),
			'right_datetime_options' => array(
					'widget' => 'single_text',
					'format' => 'dd/MM/yyyy',
					'label' => 'To',
					'attr' => array(
							'data-date-locale' => 'fr',
							'data-date-format' => 'DD/MM/YYYY'
					)
			),
			'required'               => false,
			'data_extraction_method' => 'default',
		));
	}

	public function getName() {
		return 'page_filter';
	}
}
```
A new panel will appear on top of your listing page, containing all of the fields listed above.

Read the [documentation](https://github.com/lexik/LexikFormFilterBundle/blob/master/Resources/doc/index.md) of the bundle for further informations.

---
##Form Types
###Create & Update FormTypes
Considering you can use every standard FormTypes Symfony2 provides, SfsAdmin implements some few more to help the UI being user-friendly. Here is the complete list:

- sfs_admin_field_color_picker

Allows you to display a color selector, with hexadecimal values or rgba.
```
'attr' => array(
	'data-color-format' => 'hex'
)
```

- sfs_admin_field_datetime_picker

Displays a datepicker OR a datetimepicker, depending on its configuration:
```
'attr' => array(
	'data-date-locale' => 'en',
	'data-date-format' => 'YYYY-MM-DD HH:mm:ss'
)
```

- sfs_admin_field_select
- sfs_admin_field_select_entity

Just like sfs_admin_field_select except it is related to an entity class.

- sfs_admin_field_select_list
- sfs_admin_field_select_list_entity

Just like sfs_admin_field_select_list except it is related to an entity class.

- sfs_admin_field_slider

Replaces a simple text input by a slider. Here is the default configuration, that you can override:
```
'attr' => array(
	'class' => 'slider',
	'data-step'	=> 1,
	'data-type' => 'single',
	'data-min' => 0,
	'data-max' => 100,
	'data-disable' => false,
	'data-postfix' => ''
)
```
Take a look at [IonRangeSlider](http://ionden.com/a/plugins/ion.rangeSlider/en.html) for further informations.

- sfs_admin_field_switch

Displays a simple on/off button, instead of a checkbox. It is basically the same principle, except you can write values inside it.
```
'attr' => array(
	'data-on-text' => 'enabled',
	'data-off-text' => 'disabled',
	'data-label-text' => 'content',
	'data-on-color' => 'success',
	'data-off-color' => 'danger'
)
```

- sfs_admin_field_tag
- sfs_admin_field_tag_entity

Just like sfs_admin_field_tag except it is related to an entity class.

###Filter FormTypes
To be written

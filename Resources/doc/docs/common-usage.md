# Common Usage
Integrate SfsAdmin in your own Symfony3 webiste

---
##Create an admin entity
Considering you have an entity *Project\DemoBundle\Entity\Page* you want to manage. You need to create an Admin class, called in the core of SfsAdmin as Admin Resource. This class will be related to a specific entity, and it will help you to override functionalities applied to it.
```
<?php

namespace Project\DemoBundle\Admin;

use Sfs\AdminBundle\Controller\AdminController;
use \Project\DemoBundle\Admin\Form\Type\PageType;

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
		$updateForm = $this->createAdminForm(PageType::class, $object);

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
Build your form just like you would do in Symfony3, with one exception: use the methods *addTab()*, *endTab()*, *addBlock()*, *endBlock()* to contain your fields, as much as you want. It will help the engine to properly design the form.

---
##Filter form
SfsAdmin supports natively the bundle [LexikFormFilterBundle](https://github.com/lexik/LexikFormFilterBundle) to generate filters. By default none will appear, as you need to create your own filter forms.

In your Admin Resource, you need to set the filter form class:
```
use \Project\DemoBundle\Admin\Form\Filter\PageFilterType;
...

	public function setFilterForm() {
		$this->filterForm = $this->createForm(PageFilterType::class);
	}
```

Now for your form:
```
<?php

namespace Project\DemoBundle\Admin\Form\Filter;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\NumberFilterType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;
use Symfony\Component\Form\FormBuilderInterface;
use Sfs\AdminBundle\Form\AbstractFilterType;
use Sfs\AdminBundle\Form\Filter\BooleanFilter;
use Sfs\AdminBundle\Form\Filter\DateTimeRangeFilter;

class TuneFilterType extends AbstractFilterType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('id', NumberFilterType::class);
		$builder->add('title', TextFilterType::class);
		$builder->add('enabled', BooleanFilter::class');
		$builder->add('updatedAt', DateTimeRangeFilter::class, array(
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
Considering you can use every standard FormTypes Symfony3 provides, SfsAdmin implements some few more to help the UI being user-friendly. Here is the complete list:

- *Sfs\AdminBundle\Form\Type\ColorPickerType::class*

Allows you to display a color selector, with hexadecimal values or rgba.
```
$builder->add('colorProperty', ColorPickerType::class, array(
    'attr' => array(
        'data-format' => 'hex'
    )
));
```

---

- *Sfs\AdminBundle\Form\Type\DateTimePickerType::class*

Displays a datepicker or a datetimepicker, depending on its configuration:
```
$builder->add('datetimeProperty', DateTimePickerType::class, array(
    'attr' => array(
        'data-date-locale' => 'en',
        'data-date-format' => 'YYYY-MM-DD HH:mm:ss'
    )
));
```

---

- *Sfs\AdminBundle\Form\Type\SelectType::class*
```
$builder->add('property', SelectType::class, array(
    'multiple' => false,
    'attr' => array(
		'data-style' => 'btn-white',
		'data-live-search' => false
    )
));
```
The *show-tick* class will display ticks aside the selected element(s).
As this type heritates of *ChoiceType::class*, refer to the [Symfony doc](http://symfony.com/doc/3.1/reference/forms/types/choice.html)

---

- *Sfs\AdminBundle\Form\Type\SelectEntityType::class*

Just like *SelectType::class* except it is related to an entity class.
```
$builder->add('property', SelectEntityType::class, array(
    'class' => 'PathToYourEntity',
    'attr' => array(
		'data-style' => 'btn-white',
		'data-live-search' => false
    )
));
```
As this type heritates of *EntityType::class*, refer to the [Symfony doc](http://symfony.com/doc/3.1/reference/forms/types/entity.html)

---

- *Sfs\AdminBundle\Form\Type\SelectListType::class*

```
$builder->add('property', SelectListType::class, array(
    'multiple' => true
));
```
As this type heritates of *ChoiceType::class*, refer to the [Symfony doc](http://symfony.com/doc/3.1/reference/forms/types/choice.html)

---

- *Sfs\AdminBundle\Form\Type\SelectListEntityType::class*

Just like *SelectListType::class* except it is related to an entity class.

```
$builder->add('property', SelectListEntityType::class, array(
    'class' => 'PathToYourEntity',
    'attr' => array(
		'data-style' => 'btn-white',
		'data-live-search' => false
    )
));
```

As this type heritates of *EntityType::class*, refer to the [Symfony doc](http://symfony.com/doc/3.1/reference/forms/types/entity.html)

---

- *Sfs\AdminBundle\Form\Type\SliderType::class*

Replaces a simple text input by a slider. Here is the default configuration, that you can override:
```
$builder->add('integerProperty', SliderType::class, array(
    'attr' => array(
        'class' => 'slider',
        'data-step'	=> 1,
        'data-type' => 'single',
        'data-min' => 0,
        'data-max' => 100,
        'data-disable' => false,
        'data-postfix' => ''
    )
));
```
Take a look at [IonRangeSlider](http://ionden.com/a/plugins/ion.rangeSlider/en.html) for further informations.

---

- *Sfs\AdminBundle\Form\Type\SwitchType::class*

Displays a simple on/off button, instead of a checkbox. It is basically the same principle, except it has a different design & you can write labels inside it.
```
$builder->add('booleanProperty', SwitchType::class, array(
    'attr' => array(
        'data-on-text' => 'enabled',
        'data-off-text' => 'disabled',
        'data-label-text' => 'content',
        'data-on-color' => 'success',
        'data-off-color' => 'danger'
    )
));
```
---

- *Sfs\AdminBundle\Form\Type\TagType::class*
```
$builder->add('property', TagType::class, array());
```
As this type heritates of *ChoiceType::class*, refer to the [Symfony doc](http://symfony.com/doc/3.1/reference/forms/types/choice.html)

---

- *Sfs\AdminBundle\Form\Type\TagEntityType::class*

Just like *TagType::class* except it is related to an entity class.
```
$builder->add('property', TagEntityType::class, array(
    'class' => 'PathToYourEntity',
    'multiple' => true,
    'required' => false
));
```

As this type heritates of *EntityType::class*, refer to the [Symfony doc](http://symfony.com/doc/3.1/reference/forms/types/entity.html)

---

###Filter FormTypes

LexikFormFilterBundle provides barely all the types you could need. Have a look at its' [documentation](https://github.com/lexik/LexikFormFilterBundle/blob/v5.0.1/Resources/doc/provided-types.md).

SfsAdminBundle provides a few more, to get a better integration in the design & add some functionnality:

- *Sfs\AdminBundle\Form\Filter\BooleanFilter::class*

```
$builder->add('booleanProperty', BooleanFilter::class);
```

---

- *Sfs\AdminBundle\Form\Filter\DateTimePickerFilter::class*

```
$builder->add('dateTimePickerProperty', DateTimePickerFilter::class, array(
    'attr' => array(
        'data-date-locale' => 'en',
        'data-date-format' => 'YYYY-MM-DD HH:mm:ss'
    )
));
```

---

- *Sfs\AdminBundle\Form\Filter\DateTimeRangeFilter::class*

```
$builder->add('dateTimeRangeProperty', DateTimeRangeFilter::class, array(
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
    )
));
```

---

- *Sfs\AdminBundle\Form\Filter\SelectEntityFilter::class*

```
$builder->add('entityProperty', SelectEntityFilter::class, array(
    'class' => 'PathToYourEntity',
    'attr' => array(
		'data-style' => 'btn-white',
		'data-live-search' => false
    )
));
```

---

- *Sfs\AdminBundle\Form\Filter\TagEntityFilter::class*

```
$builder->add('entityProperty', TagEntityFilter::class, array(
    'class' => 'PathToYourEntity',
    'multiple' => true,
    'required' => false
));
```

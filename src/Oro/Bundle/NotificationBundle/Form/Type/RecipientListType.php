<?php

namespace Oro\Bundle\NotificationBundle\Form\Type;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\VarDumper\VarDumper;

use Oro\Bundle\FormBundle\Utils\FormUtils;

class RecipientListType extends AbstractType
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'users',
            'oro_user_organization_acl_multiselect',
            [
                'required' => false,
                'label'    => 'oro.user.entity_plural_label'
            ]
        );

        $builder->add(
            'groups',
            'entity',
            [
                'label'       => 'oro.user.group.entity_plural_label',
                'class'       => 'OroUserBundle:Group',
                'property'      => 'name',
                'multiple'      => true,
                'expanded'      => true,
                'empty_value'   => '',
                'empty_data'    => null,
                'required'      => false,
            ]
        );

        $builder->add(
            'email',
            'email',
            ['label' => 'oro.notification.emailnotification.email.label', 'required' => false]
        );

        $builder->add(
            'owner',
            'checkbox',
            ['label' => 'oro.notification.emailnotification.owner.label', 'required' => false]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'           => 'Oro\Bundle\NotificationBundle\Entity\RecipientList',
                'intention'            => 'recipientlist',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'oro_notification_recipient_list';
    }
}

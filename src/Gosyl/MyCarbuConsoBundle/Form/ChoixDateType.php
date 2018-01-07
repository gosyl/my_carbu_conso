<?php

namespace Gosyl\MyCarbuConsoBundle\Form;

use Doctrine\ORM\EntityManager;
use Gosyl\MyCarbuConsoBundle\Form\Transformers\ModeleTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoixDateType extends AbstractType {


    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('dateDebut', DateType::class, array(
            'format' => 'dd/MM/yyyy',
            'required' => true,
            'widget' => 'single_text',
            'label' => 'Entre le',
            'empty_data' => '01/01/2017'
        ));

        $builder->add('dateFin', DateType::class, array(
            'format' => 'dd/MM/yyyy',
            'required' => false,
            'widget' => 'single_text',
            'label' => 'et le',
            'empty_data' => date('d/m/Y')
        ));

        $builder->add('btnValiderChoixDate', ButtonType::class, array('label' => 'Valider', 'attr' => array('style' => "vertical-align: baseline;")));
        $builder->add('btnRAZDate', ButtonType::class, array('label' => 'Effacer les dates', 'attr' => array('style' => 'vertical-align: baseline;')));
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'method' => 'POST',
            'id' => 'formChoixDate',
            'legend' => 'Choix de la période'
        ]);
    }

    public function getBlockPrefix() {
        return '';
    }

    public function buildView(FormView $view, FormInterface $form, array $options) {
        foreach ($options as $key => $value) {
            $view->vars[$key] = $value;
        }
    }

    public function getName() {
        return $this->getBlockPrefix();
    }
}

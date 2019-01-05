<?php

namespace Gosyl\MyCarbuConsoBundle\Form;

use Doctrine\ORM\EntityManager;
use Gosyl\MyCarbuConsoBundle\Form\Transformers\ModeleTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjoutVehiculeType extends AbstractType {
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var ModeleTransformer
     */
    protected $oModeleTransformer;

    public function __construct(EntityManager $em) {
        $this->em = $em;
        $this->oModeleTransformer = new ModeleTransformer($this->em);
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->setAttribute('method', $options['method'])
            ->setAttribute('id', $options['id'])
            ->setAttribute('legend', $options['legend'])
            ->setAttribute('name', $options['id']);

        $builder->add('idModeleUser', HiddenType::class, [
            'mapped' => false
        ]);

        $builder->add('nomVehicule', TextType::class, [
            'label' => 'Nom du véhicule',
            'required' => true,
            'attr' => [
                'placeholder' => 'Ex : Mon véhicule',
                'id' => 'nomVehicule'
            ]
        ]);

        $builder->add('marque', TextType::class, [
            'mapped' => false,
            'attr' => [
                'placeHolder' => 'RENAULT'
            ]
        ]);

        $builder->add('hiddenIdMarque', HiddenType::class, ['mapped' => false]);

        $builder->add('nomCommercial', ChoiceType::class, [
            'mapped' => false,
            'attr' => ['disabled'=>'disabled'],
            'expanded' => false,
            'multiple' => false,
            'placeholder' => 'Veuillez faire un choix'
        ]);

        $builder->add('energie', ChoiceType::class, [
            'mapped' => false,
            'attr' => ['disabled'=>'disabled'],
            'expanded' => false,
            'multiple' => false,
            'placeholder' => 'Veuillez faire un choix'
        ]);

        $builder->add('boiteVitesse', ChoiceType::class, [
            'mapped' => false,
            'attr' => ['disabled'=>'disabled'],
            'expanded' => false,
            'multiple' => false,
            'placeholder' => 'Veuillez faire un choix'
        ]);

        $builder->add('carrosserie', ChoiceType::class, [
            'mapped' => false,
            'attr' => ['disabled'=>'disabled'],
            'expanded' => false,
            'multiple' => false,
            'placeholder' => 'Veuillez faire un choix'
        ]);

        $builder->add('puissanceFiscale', ChoiceType::class, [
            'mapped' => false,
            'attr' => ['disabled'=>'disabled'],
            'expanded' => false,
            'multiple' => false,
            'placeholder' => 'Veuillez faire un choix'
        ]);

        $builder->add('typeMine', TextType::class, [
            'mapped' => false,
            'attr' => [
                'placeholder' => 'JZ0NA6'
            ]
        ]);

        $builder->add('anneeMiseEnCirculation', TextType::class, [
            'attr' => [
                'placeholder' => date('Y')
            ],
            'label' => 'Année mise en circulation'
        ]);

        $builder->add('kilometrageInit', TextType::class, [
            'attr' => [
                'placeholder' => '123.45'
            ],
            'label' => 'Kilométrage (km)'
        ]);

        $builder->add('modele', HiddenType::class);
        $builder->get('modele')->addModelTransformer($this->oModeleTransformer);

        $builder->add('btnRechercheVehicule', ButtonType::class, [
            'label' => 'Rechercher',
            'attr' => [
                'type' => 'button',
                'disabled' => 'disabled'
            ]
        ]);

        $builder->add('btnNewRecherche', ButtonType::class, [
            'label' => 'Modifier véhicule',
            'attr' => [
                'type' => 'button'
            ]
        ]);

        $builder->add('btnModifRecherche', ButtonType::class, [
            'label' => 'Modifier la recherche'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'method' => 'POST',
            'id' => 'formAjoutVehicule',
            'legend' => 'Ajout d\'un véhicule',
            'data_class' => 'Gosyl\MyCarbuConsoBundle\Entity\MccModelesUsers',
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

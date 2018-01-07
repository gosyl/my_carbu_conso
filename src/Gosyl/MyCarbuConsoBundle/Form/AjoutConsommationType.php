<?php

namespace Gosyl\MyCarbuConsoBundle\Form;

use Doctrine\ORM\EntityManager;
use Gosyl\MyCarbuConsoBundle\Form\Transformers\ModeleUserTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjoutConsommationType extends AbstractType {
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var ModeleUserTransformer
     */
    protected $modeleUserTransformer;

    public function __construct(EntityManager $em) {
        $this->em = $em;
        $this->modeleUserTransformer = new ModeleUserTransformer($this->em);
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('quantite', NumberType::class, [
            'label' => 'Quantité du plein (l)',
            'attr' => [
                'placeholder' => '12.34'
            ],
            'scale' => 2
        ]);

        $builder->add('prix', NumberType::class, [
            'label' => 'Prix du plein (€)',
            'attr' => [
                'placeholder' => '12.34'
            ],
            'scale' => 2
        ]);

        $builder->add('distance', NumberType::class, [
            'label' => 'Distance parcouru (km)',
            'attr' => [
                'placeholder' => '120.34'
            ],
            'scale' => 2
        ]);

        $builder->add('adresse', TextareaType::class, [
            'required' => false,
            'attr' => [
                'placeholder' => 'Adresse de la station service',
                'style' => 'resize: none;'
            ]
        ]);

        $builder->add('datePlein', DateType::class, [
            'label' => 'Date du plein',
            'format' => 'dd/MM/yyyy',
            'widget' => 'single_text',
            'html5' => false,
            'attr' => [
                'placeholder' => date('d/m/Y')
            ]
        ]);

        $builder->add('modeleUser', HiddenType::class);
        $builder->get('modeleUser')->addModelTransformer($this->modeleUserTransformer);

        $builder->add('idConso', HiddenType::class, [
            'mapped' => false,
            'required' => false
        ]);

        $builder->add('btnValider', SubmitType::class, [
            'label' => 'Enregistrer'
        ]);

        $builder->add('btnCancelAjoutConso', ResetType::class, [
            'label' => 'Effacer'
        ]);

        $builder->add('kilometrageCompteur', NumberType::class, [
            'label' => 'Kilomètrage au compteur (km)',
            'attr' => [
                'placeholder' => '140034'
            ],
            'scale' => 0
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'method' => 'POST',
            'id' => 'formAjoutConsommation',
            'legend' => 'Ajout d\'une consommation',
            'data_class' => 'Gosyl\MyCarbuConsoBundle\Entity\MccConsommations',
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

<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

	$builder
      ->add('username', TextType::class, [
          'label' => 'Имя пользователя',
          'label_attr' => [
              'class' => 'control-label'
          ],
          'attr' => [
              'class' => 'form-control form-control-sm',
              'placeholder' => 'Введите имя пользователя'
          ],
          'required' => true
      ])
      ->add('plainPassword', RepeatedType::class, [
          'type' => PasswordType::class,
          'label' => false,
          'first_options'  => [
              'label' => 'Пароль',
              'label_attr' => [
		  'class' => 'control-label'
              ],
              'required' => false,
              'attr' => [
		  'class' => 'form-control form-control-sm',
		  'placeholder' => 'Введите пароль'
              ]
          ],
          'second_options' => [
              'label' => 'Пароль повторно',
              'label_attr' => [
		  'class' => 'control-label'
              ],
              'required' => false,
              'attr' => [
		  'class' => 'form-control form-control-sm',
		  'placeholder' => 'Повторите ввод пароля'
              ]
          ],
          'required' => false
      ])
      ->add('submit', SubmitType::class, [
          'label' => 'Сохранить',
          'attr' => [
              'class' => 'btn btn-sm btn-primary mx-auto',
              'style' => 'display: block;'
          ]
      ])
	;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

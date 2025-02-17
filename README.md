Hello there welcome to the PHP_Symphony project

The goal of this project is to construct a complete dynamic website 
in PHP using the Symfony framework.

The purpose of the website is to make a sort of copikat of Habitica,
on our website you can create an acount an join a group to manage your objectives.
you can only join one group and beware ! if the group reatch 0 point it going to be anihilated !

To install the project :
 - git clone https://github.com/H4K33L/PHP_Symfony.git

Go in the file :
 - cd PHP_Symfony

You need to install the depedences for symfony :
 - composer install

You also need to generate the BDD :
 - php bin/console doctrine:migrations:migrate

And after this all you need to do is to run the server with :
 - symfony server:start
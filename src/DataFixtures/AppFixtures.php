<?php

namespace App\DataFixtures;

use App\Entity\Productions;
use App\Entity\TypeUser;
use App\Entity\Employees;
use App\Entity\Jobs;
use App\Entity\Projects;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /** @var ObjectManager */
    private $manager;

    /** @var UserPasswordEncoderInterface */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        $this->manager = $manager;

        $this->loadProjects();
        $this->loadJobs();
        $this->loadUser();
        $this->loadProductions();
        $manager->flush();
    }

    private function loadTypeUser(): void
    {
        $types = [
            "Manager",
            "Employee"
        ];

        foreach ($types as $key => $name) {
            $type = (new TypeUser())->setName($name);

            $this->manager->persist($type);
            $this->addReference('type' . $key, $type);
        }
    }

    private function loadProjects(): void
    {
        $name1 = [
            "Référencement",
            "Développement du site",
            "Refonte du site",
            "Campagne Marketing",
            "Developpement de l'API"
        ];

        $name2 = [
            "de Groupama",
            "de Playstation",
            "de Peugeot",
            "de Leroy Merlin",
            "de Adidas",
            "de la FNAC",
            "de LG",
            "de Samsung",
            "de Nestlé",
            "de Nike",
            "de Leclerc",
            "de Discord",
            "de Netflix",
            "de Stadia"
        ];

        for ($i = 0; $i < 10; $i++) {
            $name = $name1[random_int(0, 4)] . " " . $name2[random_int(0, 13)];
            $x = $i+2;
            $project = (new Projects())
                ->setName($name)
                ->setDescription("Apud has gentes, quarum exordiens initium ab Assyriis ad Nili cataractas porrigitur et confinia Blemmyarum, omnes pari sorte sunt bellatores seminudi coloratis sagulis pube tenus amicti, equorum adiumento.")
                ->setSellingPrice(random_int(1500, 15000))
                ->setCreationDate(new \DateTime("now +" . $x . " day"));

            $this->manager->persist($project);
            $this->addReference("project" . $i, $project);
        }

    }

    private function loadJobs(): void
    {
        $jobs = [
            'Développeur Web',
            'Designer',
            'Manager SEO',
            'Web Marketeur',
        ];

        foreach ($jobs as $key => $name) {
            $job = (new Jobs())->setName($name);

            $this->manager->persist($job);
            $this->addReference('job' . $key, $job);
        }
    }

    private function loadUser(): void
    {
        $roles = [
            'ROLE_MANAGER',
            'ROLE_EMPLOYEE',
            'ROLE_EMPLOYEE',
            'ROLE_EMPLOYEE',
            'ROLE_EMPLOYEE',
            'ROLE_EMPLOYEE',
            'ROLE_EMPLOYEE',
            'ROLE_EMPLOYEE',
            'ROLE_EMPLOYEE',
            'ROLE_EMPLOYEE',
        ];

        $psw = [
            'azerty',
            'qwerty'
        ];

        for ($i = 0; $i < 15; $i++) {
            $name = $this->randomName();
            $firstname = $this->randomFirstName();
            $user = new User();
            $password = $psw[random_int(0,1)];
            $password = $this->encoder->encodePassword($user, $password);
            $employee = (new User())
                ->setName($name)
                ->setFirstName($firstname)
                ->setEmail($firstname . "." . $name . "@gmail.com")
                ->setHiringDate(new \DateTime("now +" . $i . " day"))
                ->setJob($this->getReference('job' . random_int(0, 3)))
                ->setCost(random_int(10, 25))
                ->setRoles([$roles[random_int(0,9)]])
                ->setPassword($password);

            $this->manager->persist($employee);
            $this->addReference('user' . $i, $employee);
        }
    }

    private function randomFirstName(): string
    {
        $fn = [
            "Jean",
            "Pierre",
            "Albert",
            "Jessica",
            "Isabelle",
            "Louis",
            "Laurent",
            "Eva",
            "Jules",
            "Juliette"
        ];

        return $fn[random_int(0, 9)];
    }

    private function randomName(): string
    {
        $name = [
            "Dupont",
            "Cortes",
            "Begum",
            "Frost",
            "Barclay",
            "Lee",
            "Novak",
            "Klein",
            "Riggs",
            "Sinclair"
        ];

        return $name[random_int(0, 9)];
    }

    private function loadProductions(): void
    {
        for ($i = 0; $i < 30; $i++) {
            $x = $i+5;
            $production = (new Productions())
                ->setUser($this->getReference("user" . random_int(0, 14)))
                ->setProject($this->getReference("project" . random_int(0, 9)))
                ->setCreationDate(new \DateTime("now +" . $x . " hour"))
                ->setHourNumber(random_int(1, 7));

            $this->manager->persist($production);
        }
    }
}

<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class DefaultControllerTest extends WebTestCase {
	public function urlProvider() {
        return array(
            array('/en', ""),
            array('/en/details/0', ""),
            array('/en/create', "You must be logged in before posting"),
            array('/en/edit/0', "You must be logged in before editing"),
            array('/en/delete/0', "You must be logged in before deleting"),
            array('/fr', ""),
            array('/fr/details/0', ""),
            array('/fr/create', "Vous devez être connecté avant de poster"),
            array('/fr/edit/0', "Vous devez être connecté avant de modifier"),
            array('/fr/delete/0', "Vous devez être connecté avant de supprimé"),
            array('/ro', ""),
            array('/ro/details/0', ""),
            array('/ro/create', "Trebuie să fii autentificat pentru a putea adăuga mesaje"),
            array('/ro/edit/0', "Trebuie să fii autentificat pentru a putea modifica mesaje"),
            array('/ro/delete/0', "Trebuie să fii autentificat pentru a putea șterge mesaje"),
        );
    }

	public function parameterLanguage() {
		return array(
			array('en', array('Latest posts', 'posts found'), array("View posts", "Create post", "Log in", "read more...", "/en/page/1", "/ro/page/1", "/fr/page/1")),
			array('ro', array('Ultimele mesaje', 'Am găsit'), array("Vizualizare mesaje", "Creare mesaj", "Autentificare", "citește tot mesajul...", "/en/page/1", "/ro/page/1", "/fr/page/1")),
			array('fr', array('Derniers messages', 'postes trouvés'), array("Voir les messages", "Créer poste", "S'identifier", "Lire la suite...", "/en/page/1", "/ro/page/1", "/fr/page/1")),
		);
	}

	public function parameterLogin() {
		return array(
			array('en', array('Sign in', 'Logout user')),
			array('fr', array('Se connecter', 'Déconnecter user')),
			array('ro', array('Conectare', 'Deconectare user')),
		);
	}

	public function parameterSearch() {
		return array(
			array("StyKoi;L79;EH87JlWo7ilP8lO,l5i;9p04]8\U-6", false),
			array('test test test', true),
		);
	}

	public function testIndex() {
		echo PHP_EOL . "TEST INDEX 302";

		$client = static::createClient();
		$client->request('GET', '/');
		$this->assertEquals(302, $client->getResponse()->getStatusCode());
		$this->assertTrue($client->getResponse()->isRedirect('/en'));
	}

	/**
     * @dataProvider urlProvider
     */
    public function testPages($url, $message) {
		echo PHP_EOL . "TEST PAGE ".$url;

        $client = self::createClient();
        $client->request('GET', $url);
		
		if($client->getResponse()->isRedirect()) {
			$client->followRedirect();
			$page = $client->getResponse()->getContent();
			$this->assertGreaterThan(0, strpos($page, $message));
		}

		$this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

	/**
	 * @dataProvider parameterLanguage
	 */
    public function testLanguage($language, $h1, $links) {
		echo PHP_EOL . "TEST LANGUAGE ".$language;

		$client = static::createClient();
		$crawler = $client->request('GET', '/'.$language);

		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		$this->assertContains($h1[0], $crawler->filter('#sidebar h1')->text());

		$str = trim($crawler->filter('h1')->last()->text());
		$int = filter_var($str, FILTER_SANITIZE_NUMBER_INT);
		$this->assertGreaterThan(0, $int);
		$this->assertContains($h1[1], $str);

		$nodeValues = $crawler->filter('a')->each(function (Crawler $node, $i) {
			$val = $node->text();
			if ($val == "") {
				$href = $node->extract(array('_text', 'href'));
				$val = $href[0][1];
			}
			return $val;
		});
		for ($i = 0; $i < count($links); $i++)
			$this->assertContains($links[$i], $nodeValues);
	}

	public function testInvalidLogin() {
		echo PHP_EOL . "TEST INVALID LOGIN";

		$client = static::createClient();
		$this->login($client, "<user>", "<pass>");

		$this->assertTrue($client->getResponse()->isRedirect('http://localhost/en/login'));
		$crawler = $client->followRedirect();
		
		$this->assertContains("Invalid credentials", $crawler->filter('div:contains("Invalid credentials.")')->text());
	}

	/**
	 * @dataProvider parameterLogin
	 */
	public function testValidLogin($language, $expected) {
		echo PHP_EOL . "TEST VALID LOGIN ".$language;

		$client = static::createClient();
		$this->login($client, "<user>", "<pass>", $language, $expected[0]);

		$this->assertTrue($client->getResponse()->isRedirect('http://localhost/' . $language));
		$crawler = $client->followRedirect();

		$this->assertContains($expected[1], $crawler->filter('a:contains(' . $expected[1] . ')')->text());
	}

	public function testCreatePost() {
		echo PHP_EOL . "TEST CREATE POST";

		$client = static::createClient();
		$this->login($client, "<user>", "<pass>");

		$this->assertTrue($client->getResponse()->isRedirect('http://localhost/en'));
		$crawler = $client->followRedirect();

		$this->assertContains("Logout user", $crawler->filter('a:contains("Logout user")')->text());

		$crawler = $client->request('GET', '/en/create');
		$buttonCrawler = $crawler->selectButton("Create post");
		$form = $buttonCrawler->form(array('message' => "test_test_test test_test_test test_test_test"));
		$client->submit($form);

		$crawler = $client->followRedirect();

		$this->assertContains("Post added", $crawler->filter('div:contains("Post added.")')->text());
	}

	public function testEditFirstPost() {
		echo PHP_EOL . "TEST EDIT FIRST POST";

		$client = static::createClient();
		$crawler = $this->login($client, "<user>", "<pass>");
		$crawler = $client->request('GET', "/en/edit/0");
		$crawler = $client->followRedirect();
		$expected = "Edit failed.";
		$this->assertContains($expected, $crawler->filter('div:contains("' . $expected . '")')->text());
	}

	public function testDeleteFirstPost() {
		echo PHP_EOL . "TEST DELETE FIRST POST";

		$client = static::createClient();
		$crawler = $this->login($client, "<user>", "<pass>");
		$crawler = $client->request('GET', "/en/delete/0");
		$crawler = $client->followRedirect();
		$expected = "Remove failed.";

		$this->assertContains($expected, $crawler->filter('div:contains("' . $expected . '")')->text());
	}

	public function testEditPost() {
		echo PHP_EOL . "TEST EDIT CREATED POST";

		$client = static::createClient();
		$search = "test_test_test test_test_test test_test_test";
		$crawler = $this->search($client, $search);
		$expected = "No posts found with the search " . $search . ".";
		$page = $client->getResponse()->getContent();
		$noPostsFound = strpos($page, $expected);

		if (strpos($page, 'href="/en/edit/') === false)
			$this->assertTrue("No message from user on first page.");

		$nodeValues = $crawler->filter('a')->each(function (Crawler $node, $i) {
			if ($node->text() == "Edit") {
				$href = $node->extract(array('_text', 'href'));
				return $href[0][1];
			}
		});
		$nodeValues = array_values(array_filter($nodeValues));
		$toEdit = $nodeValues[count($nodeValues)-1];
		$crawler = $client->request('GET', $toEdit);

		$buttonCrawler = $crawler->selectButton("Edit");
		$form = $buttonCrawler->form(array('message' => "test test test"));
		$client->submit($form);
		$client->followRedirect();

		$page = $client->getResponse()->getContent();
		$pattern = '/Post modified./';
		preg_match($pattern, $page, $matches);

		$this->assertGreaterThan(0, count($matches));
	}

	/**
	 * @dataProvider parameterSearch
	 */
	public function testSearchPost($search, $val) {
		echo PHP_EOL . "TEST SEARCH POST ".$search;

		$client = static::createClient();
		$crawler = $this->search($client, $search);

		if ($val) {
			$str = trim($crawler->filter('h1')->last()->text());
			$int = filter_var($str, FILTER_SANITIZE_NUMBER_INT);
			$this->assertGreaterThan(0, $int);
		}
		else {
			$expected = "No posts found with the search " . $search . ".";
			$this->assertContains($expected, $crawler->filter('div:contains("' . $expected . '")')->text());
		}
	}

	public function testDeletePost() {
		echo PHP_EOL . "TEST DELETE CREATED POST";

		$client = static::createClient();
		$crawler = $this->search($client, "test test test");
		$expected = "No posts found with the search test test test.";
		$page = $client->getResponse()->getContent();
		$noPostsFound = strpos($page, $expected);

		if (strpos($page, 'href="/en/delete/') === false)
			$this->assertTrue("No message from user on first page.");

		$nodeValues = $crawler->filter('a')->each(function (Crawler $node, $i) {
			if ($node->text() == "Delete") {
				$href = $node->extract(array('_text', 'href'));
				return $href[0][1];
			}
		});
		$nodeValues = array_values(array_filter($nodeValues));

		$toRemove = $nodeValues[count($nodeValues)-1];
		if ($noPostsFound === false)
			$toRemove = $nodeValues[rand(1, count($nodeValues))-1];

		$client->request('GET', $toRemove);
		$client->followRedirect();

		$page = $client->getResponse()->getContent();
		$pattern = '/Post (.*) deleted./';
		preg_match($pattern, $page, $matches);

		$this->assertGreaterThan(0, count($matches));
	}
	
	public function login($client, $user, $pass, $language = "en", $button = "Sign in") {
		$crawler = $client->request('GET', '/' . $language);
		$this->assertTrue($client->getResponse()->isSuccessful());

		$buttonCrawler = $crawler->selectButton($button);
		$form = $buttonCrawler->form(array(
			'_username' => $user,
			'_password' => $pass,
		));
		return $client->submit($form);
	}

	public function search($client, $search) {
		$crawler = $this->login($client, "<user>", "<pass>");

		$this->assertTrue($client->getResponse()->isRedirect('http://localhost/en'));
		$crawler = $client->followRedirect();

		$this->assertContains("Logout user", $crawler->filter('a:contains("Logout user")')->text());

		$crawler = $client->request('GET', '/en/create');
		$buttonCrawler = $crawler->selectButton("Search");
		$form = $buttonCrawler->form(array('search' => $search));

		return $client->submit($form);
	}

	public function debug($client, $file) {
		$f = fopen($file . ".txt", "w");
		fwrite($f, $client->getResponse()->getContent());
		fclose($f);
	}

}
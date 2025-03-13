<?php

namespace Drupal\Tests\jsonapi_response_test\Functional;

use Drupal\Component\Serialization\Json;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\node\Traits\NodeCreationTrait;

class JsonApiResponseTest extends BrowserTestBase {

  use NodeCreationTrait;

  protected static        $modules        = ['jsonapi_response_test', 'node'];
  public                  $defaultTheme   = 'olivero';

  public function testResponse(): void
  {
    $type = $this->createContentType();
    $nodeA = $this->createNode(['type' => $type->id(), 'status' => 1, 'title' => 'Test A']);
    $nodeA->save();
    $this->container->get('router.builder')->rebuild();
    $this->assertEquals(1, $nodeA->id());
    $responseIndividual = $this->drupalGet('json/api/response/test/node/individual');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->responseHeaderEquals('Content-Type', 'application/vnd.api+json');
    $dataIndividual = Json::decode($responseIndividual);
    $this->assertIsArray($dataIndividual);
    $this->assertArrayHasKey('data', $dataIndividual);
    $this->assertIsArray($dataIndividual['data']);
    $this->assertEquals('node--' . $type->id(), $dataIndividual['data']['type']);
    $this->assertEquals($nodeA->uuid(), $dataIndividual['data']['id']);
    $this->assertEquals('Test A', $dataIndividual['data']['attributes']['title']);
    $this->assertArrayHasKey('included', $dataIndividual);
    $this->assertEquals($dataIndividual['included'][0]['type'], 'user--user');
  }

}

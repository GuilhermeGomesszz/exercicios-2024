<?php

namespace Chuva\Php\WebScrapping;

use Chuva\Php\WebScrapping\Entity\Paper;
use Chuva\Php\WebScrapping\Entity\Person;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Does the scrapping of a webpage.
 */


 class Scrapper {
  public function scrap($url): array {
      $html = file_get_contents($url);
      $dom = new \DOMDocument();
      libxml_use_internal_errors(true);
      $dom->loadHTML($html);
      libxml_use_internal_errors(false);

      $papers = [];

      // Use DOMXPath to query for paper elements
      $xpath = new \DOMXPath($dom);
      $paperNodes = $xpath->query('//div[@class="paper"]');

      // Loop through each paper element and extract data
      foreach ($paperNodes as $paperNode) {
          // Extract paper title
          $titleNode = $xpath->query('.//h2', $paperNode)->item(0);
          $title = $titleNode ? $titleNode->nodeValue : '';

          // Extract authors
          $authorNodes = $xpath->query('.//p[@class="author"]', $paperNode);
          $authors = [];
          foreach ($authorNodes as $authorNode) {
              $authors[] = $authorNode->nodeValue;
          }

          // Create Paper object and add it to the array
          $papers[] = new Paper(
              // Generate a unique ID or use some other logic to set the ID
              123,
              $title,
              'Journal', // Assuming all papers are from a journal
              // Create Person objects for each author
              array_map(function ($author) {
                  return new Person($author, '');
              }, $authors)
          );
      }

      return $papers;
  }
}

// Example usage:
$scrapper = new Scrapper();
$data = $scrapper->scrap('http://127.0.0.1:5500/php/assets/origin.html');
print_r($data); // Output the scraped data for verification
<?php

require_once "bootstrap.php";
use Garden\Cli\Cli;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;

$cli = new Cli();

$cli->description('Update local Prozorro contracts copy.')
	->opt('force:f', 'Update all contracts (long!).', false, 'boolean')
	->opt('days:d', 'Only update since X days ago.', false, 'integer');

// Parse and return cli args.
$args = $cli->parse($argv, true);

$since = new DateTime("2016-05-01T00:00:01+03:00");

if ($args->getOpt("force")) {
	$since = new DateTime("2016-05-01T00:00:01+03:00");
} else if ($args->getOpt("days")) {
	$since = (new DateTime("NOW"))->sub(new DateInterval("P" . $args->getOpt("days") . "D"));
} else {
	$dql = "SELECT max(c.date_modified) AS last_contract FROM Contract c";
	$last_entry = $entityManager->createQuery($dql)->getScalarResult();

	if ($last_entry[0]["last_contract"] != "") {
		$since = new DateTime($last_entry[0]["last_contract"]);
	}
}

function get_page_since($entityManager, $since) {
	$client = new Client(['base_uri' => 'https://api.openprocurement.org/']);
	$response = $client->request('GET', "api/2.5/contracts", ['query' => ['offset' => $since->format("c")]]);

	$listing_body = json_decode($response->getBody());
	$contract_requests = [];
	foreach ($listing_body->data as $contract) {
		$contract_requests[$contract->id] = $client->getAsync("api/2.5/contracts/" . $contract->id);
	}

	$contract_responses = Promise\Utils::settle($contract_requests)->wait();
	foreach ($contract_responses as $contract_id => $contract_response) {
		if ($contract_response['state'] == "fulfilled") {
			$contract_response = json_decode($contract_response['value']->getBody(), $assoc = true)["data"];

			$contract_obj = new Contract();
			$contract_obj->id = $contract_response["id"];
			if (isset($contract_response["period"])) {
				if (isset($contract_response["period"]["startDate"])) {
					$contract_obj->start_date = new DateTime($contract_response["period"]["startDate"]);
				}
				if (isset($contract_response["period"]["endDate"])) {
					$contract_obj->end_date = new DateTime($contract_response["period"]["endDate"]);
				}
			}
			$contract_obj->identifier_id = $contract_response["suppliers"][0]["identifier"]["id"];
			$contract_obj->identifier_legal_name = $contract_response["suppliers"][0]["identifier"]["legalName"];
			$contract_obj->contract_id = $contract_response["contractID"];
			$contract_obj->payload = $contract_response;
			$contract_obj->date_modified = new DateTime($contract_response["dateModified"]);

			$entityManager->merge($contract_obj);
			$entityManager->flush();
		} else {
			print_r("Cannot retrieve contract id " . $contract_id . "\n");
		}
	}
}

print_r("Downloading contracts since " . $since->format("c")) . "!";

get_page_since($entityManager, $since);
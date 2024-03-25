<?php                                      
                                                     
namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @Route("/api")
 */
class ApiController extends AbstractFOSRestController
{
    /**
     * @Route("/getmetadata/", name="api.getmetadata", methods="GET")
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getMetadata(Request $request): Response
    {
        $data = new \stdClass();
        $persistentId = $request->get('persistentId');
        $requestUrl = $this->getParameter('dataverse_metadata_url') . $persistentId;

        if ($persistentId) {
            $client = new CurlHttpClient();
            $response = $client->request('GET', $requestUrl);

            if ($response->getStatusCode() !== 200) {
                return $this->handleView($this->view($data, Response::HTTP_I_AM_A_TEAPOT));
            }
            $data = $this->getFieldsDataFromMetadata($response->getContent());
        }

        return $this->handleView($this->view($data, Response::HTTP_OK));
    }

    /**
     * @param string $metadata
     *
     * @return array
     */
    protected function getFieldsDataFromMetadata(string $metadata): array
    {
        $fields = [];
        $data = json_decode($metadata, true, 512, JSON_THROW_ON_ERROR);

        if (
            is_array($data)
            && isset($data['datasetVersion'])
            && isset($data['datasetVersion']['metadataBlocks'])
            && isset($data['datasetVersion']['metadataBlocks']['citation'])
            && isset($data['datasetVersion']['metadataBlocks']['citation']['fields'])
            && is_iterable($data['datasetVersion']['metadataBlocks']['citation']['fields'])
        ) {
            foreach ($data['datasetVersion']['metadataBlocks']['citation']['fields'] as $field) {
                if (isset($field['typeName'])) {
                    $fields[$field['typeName']] = $field['value'];
                }
            }
        }
        return $fields;
    }
}

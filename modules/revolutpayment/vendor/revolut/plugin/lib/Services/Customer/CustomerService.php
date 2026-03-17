<?php

declare(strict_types=1);

namespace Revolut\Plugin\Services\Customer;

use Exception;
use Revolut\Plugin\Infrastructure\Api\Customers\CustomersApiInterface;
use Revolut\Plugin\Infrastructure\Api\Customers\DTOs\CustomerDTO;
use Revolut\Plugin\Services\Config\Api\ConfigInterface;
use Revolut\Plugin\Types\ApiEnv;
use Revolut\Plugin\Types\Email;
use Revolut\Plugin\Types\PhoneNumber;

class CustomerService implements CustomerServiceInterface
{
    /** @var CustomerRepositoryInterface */
    private $repo;

    /** @var ConfigInterface */
    private $config;

    /** @var CustomersApiInterface */
    private $customerApi;

    public function __construct(
        CustomersApiInterface $customerApi,
        CustomerRepositoryInterface $repo,
        ConfigInterface $config
    ) {
        $this->repo = $repo;
        $this->config = $config;
        $this->customerApi = $customerApi;
    }

    public function create(CustomerParams $params): ?CustomerDTO
    {
        if (!$params->getEmail()) {
            return null;
        }

        $customerModel = $this->repo->findByPlatformId($params->getPlatformCustomerId(), ApiEnv::of($this->config->getMode()));

        if ($customerModel) {
            $apiCustomer = $this->customerApi->getById($customerModel->getRevolutId());

            if ($apiCustomer) {
                if ($params->getPhone() && !$params->getPhone()->equals($apiCustomer->phone)) {
                    $this->customerApi->update($apiCustomer, ['phone' => $params->getPhone()->getValue()]);
                }

                return $apiCustomer;
            }
        }


        $apiCustomer = $this->customerApi->getByEmail($params->getEmail());

        if (!$apiCustomer) {
            $apiCustomer =  $this->customerApi->create($params->getEmail(), $params->getFullName(), $params->getPhone());
        }

        if ($customerModel) {
            $customerModel->setRevolutId($apiCustomer->id);
            $this->repo->update($customerModel);
            return $apiCustomer;
        }

        $customerModel = new CustomerModel(
            $apiCustomer->id,
            $params->getPlatformCustomerId(),
            ApiEnv::of($this->config->getMode())
        );

        $this->repo->add($customerModel);
        return $apiCustomer;
    }
}

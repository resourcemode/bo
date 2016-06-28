<?php

/**
 * Class Customer
 */
class Customer
{
    protected $first_name;

    protected $last_name;

    protected $address;

    protected $phone;

    // for sample input amount
    protected $deposit_amount = 10;

    // for sample input amount
    protected $withdraw_amount = 6;

    // for sample input amount
    protected $allowed_prearrange_amount= 2;

    public function  __construct(array $customer_info)
    {
        foreach ($customer_info as $fieldName => $customer) {
            if (empty($fieldName)) {
                continue;
            }

            $this->$fieldName =  $customer_info[$fieldName];
        }
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param mixed $first_name
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param mixed $last_name
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return int
     */
    public function getWithdrawAmount()
    {
        return $this->withdraw_amount;
    }

    /**
     * @param int $withdraw_amount
     */
    public function setWithdrawAmount($withdraw_amount)
    {
        $this->withdraw_amount = $withdraw_amount;
    }

    /**
     * @return int
     */
    public function getAllowedPrearrangeAmount()
    {
        return $this->allowed_prearrange_amount;
    }

    /**
     * @param int $allowed_prearrange_amount
     */
    public function setAllowedPrearrangeAmount($allowed_prearrange_amount)
    {
        $this->allowed_prearrange_amount = $allowed_prearrange_amount;
    }

    /**
     * @return int
     */
    public function getDepositAmount()
    {
        return $this->deposit_amount;
    }

    /**
     * @param int $deposit_amount
     */
    public function setDepositAmount($deposit_amount)
    {
        $this->deposit_amount = $deposit_amount;
    }
}

/**
 * Class Account
 */
class Account implements AccountInterface
{
    use ModelTrait;

    protected $customer;

    public function __construct(Customer $customer, AccountModel $accountModel)
    {
        $this->setModel($accountModel);

        $this->customer = $customer;
    }

    public function open()
    {
        return $this->getModel()->store($this->customer);
    }

    public function close()
    {
        return $this->getModel()->delete($this->customer);
    }

    public function display()
    {
        return $this->getModel()->fetch($this->customer);
    }

    public function deposit()
    {
        // get the customer account
        $account = $this->getModel()->fetch($this->customer);

        // get the current balance
        $balance = $account->balance;

        // add the new amount
        $account['balance'] = ($balance + $this->customer->getDepositAmount());

        // update the record in the db
        return [
            'account'  => $account,
            'customer' => $this->getModel()->update($this->customer)
        ];
    }

    public function withdraw()
    {
        // get the customer account
        $account = $this->getModel()->fetch($this->customer);

        // get the current balance
        $balance = $account['balance'];

        // add the new amount
        $account['balance'] = ($balance - $this->customer->getWithdrawAmount());

        // update the record in the db
        return [
            'account'   => $account,
            'customer'  => $this->getModel()->update($this->customer)
        ];
    }

    public function preArrangedNegativeBalance()
    {
        $allowedPreArrangementAmount = $this->customer->getAllowedPrearrangeAmount();

        $account = $this->getModel()->fetch($this->customer);

        $accountBalance = $account['balance'] + $allowedPreArrangementAmount;

        // check whether user is still allow to withdraw
        if ($accountBalance < $this->customer->getWithdrawAmount()) {
            throw new \Exception('Based on your agreement, you are only allowed to withdraw up to ' . $accountBalance);
        }

        $account['balance'] = $accountBalance - $this->customer->getWithdrawAmount();

        // update the record in the db
        return [
            'account'   => $account,
            'customer'  => $this->getModel()->update($this->customer)
        ];
    }
}

/**
 * This is a sample model
 *
 * Class AccountModel
 */
class AccountModel
{
    // this is sample and dummy only
    public $balance = 100;

    /**
     * Account store
     *
     * @param Customer $customer
     * @return array
     */
    public function store(Customer $customer)
    {
        // create record

        // return dummy data
        return [
            'message'        => 'Account Created',
            'account_id'     => uniqid(),
            'customer'       => $customer,
            'balance'        => $this->balance
        ];
    }

    /**
     * Account udpate
     *
     * @param Customer $customer
     * @return array
     */
    public function update(Customer $customer)
    {
        // update record

        // return dummy data
        return [
            'message'        => 'Account Updated',
            'account_id'     => uniqid(),
            'customer'       => $customer,
            'balance'        => $this->balance
        ];
    }

    /**
     * Delete Account
     *
     * @param Customer $customer
     * @return array
     */
    public function delete(Customer $customer)
    {
        // soft deleted record

        // return dummy data
        return [
            'message'        => 'Account Deleted',
            'account_id'     => uniqid(),
            'customer'       => $customer,
            'balance'        => $this->balance
        ];
    }

    /**
     * Fetch Account
     *
     * @param Customer $customer
     * @return array
     */
    public function fetch(Customer $customer)
    {
        // fetch dummy data
        return [
            'message'        => 'Account Fetched',
            'account_id'     => uniqid(),
            'customer'       => $customer,
            'balance'        => $this->balance
        ];
    }
}

/**
 * All classes that uses our account class shall implement the methods in this interface
 *
 * Interface AccountInterface
 */
interface AccountInterface
{
    public function open();

    public function close();

    public function display();

    public function deposit();

    public function withdraw();

    public function preArrangedNegativeBalance();
}

/**
 * Use this trait in all of our model or repository
 *
 * Class ModelTrait
 */
trait ModelTrait
{

    protected $model;

    /**
     * Get a new instance of the model
     *
     * @return mixed
     */
    public function getModel()
    {
        return new $this->model;
    }

    /**
     * Get the name of the stored model
     *
     * @return string
     */
    public function getModelClass()
    {
        return $this->model;
    }

    /**
     * Set the model used by the class
     *
     * @param void
     */
    public function setModel($model)
    {
        $this->model = get_class($model);
    }
}

/**
 * This is only a sample test
 *
 * We are not using PHPUnit or any similar tool, this is just a sample on how to execute the above code
 *
 * Class Test
 */
class Test
{
    protected $data;

    protected $customer;

    public function  __construct()
    {
        $data  = [
            'first_name' => 'Michael',
            'last_name'  => 'Favila',
            'phone'      => '+65 9152 8457',
            'address'    => 'Singapore, Singapore',
            'deposit_amount'  => 20,
            'withdraw_amount' => 10,
        ];

        $this->data = $data;

        $this->customer = new Customer($data);
    }

    public function testOpenAccount()
    {
        $account = new Account($this->customer, new AccountModel());

        $process = $account->open();

        $result = 'Fail';
        if ($process['message'] == 'Account Created') {
            $result = 'Pass';
        }

        print __FUNCTION__ . ': ' .$result . "\n";
    }

    public function testCloseAccount()
    {
        $account = new Account($this->customer, new AccountModel());

        $process = $account->close();

        $result = 'Fail';
        if ($process['message'] == 'Account Deleted') {
            $result = 'Pass';
        }

        print __FUNCTION__ . ': ' .$result . "\n";
    }

    public function testDisplayAccount()
    {
        $account = new Account($this->customer, new AccountModel());

        $process = $account->display();

        $result = 'Fail';
        if ($process['message'] == 'Account Fetched' && $process['customer'] instanceof $this->customer) {
            $result = 'Pass';
        }

        print __FUNCTION__ . ': ' .$result . "\n";
    }

    public function testDepositAccount()
    {
        $account = new Account($this->customer, new AccountModel());

        $process = $account->deposit();

        $result = 'Fail';

        if ($process['account']['balance'] == 20) {
            $result = 'Pass';
        }

        print __FUNCTION__ . ': ' .$result . "\n";
    }

    public function testWithdrawAccount()
    {
        $account = new Account($this->customer, new AccountModel());

        $process = $account->withdraw();

        $result = 'Fail';
        if ($process['account']['balance'] == 90) {
            $result = 'Pass';
        }

        print __FUNCTION__ . ': ' .$result . "\n";
    }

    public function testPreArrangedNegativeBalance()
    {
        $account = new Account($this->customer, new AccountModel());

        $process = $account->preArrangedNegativeBalance();

        $result = 'Fail';
        if ($process['account']['balance'] == 92) {
            $result = 'Pass';
        }

        print __FUNCTION__ . ': ' .$result . "\n";
    }
}

$execute = new Test();
$execute->testOpenAccount();
$execute->testCloseAccount();
$execute->testDisplayAccount();
$execute->testDepositAccount();
$execute->testWithdrawAccount();
$execute->testPreArrangedNegativeBalance();
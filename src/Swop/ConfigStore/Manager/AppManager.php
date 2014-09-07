<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swop\ConfigStore\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Swop\ConfigStore\Exception\UnknownAppException;
use Swop\ConfigStore\Exception\UnknownGroupException;
use Swop\ConfigStore\Model\App;
use Swop\ConfigStore\Model\AppGroup;
use Swop\ConfigStore\Repository\AppRepository;

class AppManager
{
    /** @var AppRepository */
    protected $appRepository;
    /** @var ObjectRepository */
    protected $groupRepository;
    /** @var ObjectManager */
    protected $persistenceManager;

    /**
     * @param AppRepository                                 $appRepository
     * @param \Doctrine\Common\Persistence\ObjectRepository $groupRepository
     * @param \Doctrine\Common\Persistence\ObjectManager    $persistenceManager
     */
    public function __construct(
        AppRepository $appRepository,
        ObjectRepository $groupRepository,
        ObjectManager $persistenceManager
    ) {
        $this->appRepository      = $appRepository;
        $this->groupRepository    = $groupRepository;
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * Find an app which have the given name
     *
     * @param string $appName
     *
     * @return App
     *
     * @throws \Swop\ConfigStore\Exception\UnknownAppException
     */
    public function getByName($appName)
    {
        if (null === $app = $this->appRepository->findAppByName($appName)) {
            throw new UnknownAppException($appName);
        }

        return $app;
    }

    /**
     * Get all apps
     *
     * @return array
     */
    public function all()
    {
        return $this->appRepository->findAllApps();
    }

    /**
     * Find a group which have the given id
     *
     * @param int $groupId
     *
     * @return AppGroup
     *
     * @throws \Swop\ConfigStore\Exception\UnknownGroupException
     */
    public function getGroup($groupId)
    {
        if (null === $group = $this->groupRepository->find($groupId)) {
            throw new UnknownGroupException($groupId);
        }

        return $group;
    }

    /**
     * Get all groups
     *
     * @return array
     */
    public function allGroups()
    {
        return $this->groupRepository->findAll();
    }

    /**
     * Find an app which have the given access key
     *
     * @param string $accessKey
     *
     * @return App
     *
     * @throws \Swop\ConfigStore\Exception\UnknownAppException
     *
     */
    public function getByAccessKey($accessKey)
    {
        if (null === $app = $this->appRepository->findAppByAccessKey($accessKey)) {
            throw new UnknownAppException($accessKey);
        }

        return $app;
    }

    /**
     * Save an app
     *
     * @param App $app
     */
    public function saveApp(App $app)
    {
        $this->saveObject($app);
    }

    /**
     * Delete an app
     *
     * @param App $app
     */
    public function deleteApp(App $app)
    {
        $this->deleteObject($app);
    }

    /**
     * Save an app group
     *
     * @param AppGroup $group
     */
    public function saveGroup(AppGroup $group)
    {
        $this->saveObject($group);
    }

    /**
     * Delete an app group
     *
     * @param AppGroup $group
     */
    public function deleteGroup(AppGroup $group)
    {
        $this->deleteObject($group);
    }

    /**
     * Save an app or a group
     *
     * @param App|AppGroup $object
     */
    protected function saveObject($object)
    {
        $this->persistenceManager->persist($object);
        $this->persistenceManager->flush($object);
    }

    /**
     * Delete an app or a group
     *
     * @param App|AppGroup $object
     */
    protected function deleteObject($object)
    {
        $this->persistenceManager->remove($object);
        $this->persistenceManager->flush($object);
    }
}

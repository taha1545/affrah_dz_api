<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Pubsub;

class ConfluentCloud extends \Google\Model
{
  /**
   * @var string
   */
  public $bootstrapServer;
  /**
   * @var string
   */
  public $clusterId;
  /**
   * @var string
   */
  public $gcpServiceAccount;
  /**
   * @var string
   */
  public $identityPoolId;
  /**
   * @var string
   */
  public $state;
  /**
   * @var string
   */
  public $topic;

  /**
   * @param string
   */
  public function setBootstrapServer($bootstrapServer)
  {
    $this->bootstrapServer = $bootstrapServer;
  }
  /**
   * @return string
   */
  public function getBootstrapServer()
  {
    return $this->bootstrapServer;
  }
  /**
   * @param string
   */
  public function setClusterId($clusterId)
  {
    $this->clusterId = $clusterId;
  }
  /**
   * @return string
   */
  public function getClusterId()
  {
    return $this->clusterId;
  }
  /**
   * @param string
   */
  public function setGcpServiceAccount($gcpServiceAccount)
  {
    $this->gcpServiceAccount = $gcpServiceAccount;
  }
  /**
   * @return string
   */
  public function getGcpServiceAccount()
  {
    return $this->gcpServiceAccount;
  }
  /**
   * @param string
   */
  public function setIdentityPoolId($identityPoolId)
  {
    $this->identityPoolId = $identityPoolId;
  }
  /**
   * @return string
   */
  public function getIdentityPoolId()
  {
    return $this->identityPoolId;
  }
  /**
   * @param string
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * @param string
   */
  public function setTopic($topic)
  {
    $this->topic = $topic;
  }
  /**
   * @return string
   */
  public function getTopic()
  {
    return $this->topic;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfluentCloud::class, 'Google_Service_Pubsub_ConfluentCloud');

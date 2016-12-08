/*
 *  Copyright 2009-2016 Weibo, Inc.
 *
 *    Licensed under the Apache License, Version 2.0 (the "License");
 *    you may not use this file except in compliance with the License.
 *    You may obtain a copy of the License at
 *
 *        http://www.apache.org/licenses/LICENSE-2.0
 *
 *    Unless required by applicable law or agreed to in writing, software
 *    distributed under the License is distributed on an "AS IS" BASIS,
 *    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *    See the License for the specific language governing permissions and
 *    limitations under the License.
 */

package controllers

import (
	log "github.com/Sirupsen/logrus"
	"weibo.com/opendcp/imagebuild/code/errors"
	"weibo.com/opendcp/imagebuild/code/web/models"
)

/**
根据id获取build历史记录
 */
type BuildProgressController struct {
	BasicController
}

func (c *BuildProgressController) Get() {

	log.Info("BuildProgressController: %s", c.Ctx.Request.Form)

	id, _ := c.GetInt("id", -1)
	if id == -1 {
		log.Error("Id should not be empty when quering build progress")
		resp := models.BuildResponse(
			errors.PARAMETER_INVALID,
			"",
			errors.ErrorCodeToMessage(errors.PARAMETER_INVALID))
		c.Data["json"] = resp
		c.ServeJSON(true)
		return
	}

	log.Infof("Query build progress, id is %s", id)
	history := models.AppServer.GetBuildHistory(id)

	var resp interface{}
	if history == nil {
		resp = models.BuildResponse(
			errors.INTERNAL_ERROR,
			"",
			errors.ErrorCodeToMessage(errors.INTERNAL_ERROR))
	} else {
		resp = models.BuildResponse(
			errors.OK,
			history.State(),
			errors.ErrorCodeToMessage(errors.OK))
	}

	c.Data["json"] = resp
	c.ServeJSON(true)
	return
}

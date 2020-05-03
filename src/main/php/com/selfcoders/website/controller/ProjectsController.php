<?php
namespace com\selfcoders\website\controller;

use com\selfcoders\website\exception\ForbiddenException;
use com\selfcoders\website\exception\NotFoundException;
use com\selfcoders\website\TwigRenderer;

class ProjectsController extends AbstractController
{
    public function listProjects()
    {
        return TwigRenderer::render("projects");
    }

    public function showProject($params)
    {
        $project = $this->projects->byName($params["name"]);

        if ($project === null) {
            throw new NotFoundException;
        }

        return TwigRenderer::render("project", [
            "project" => $project
        ]);
    }

    public function getResource($params)
    {
        $project = $this->projects->byName($params["name"]);

        if ($project === null) {
            throw new NotFoundException;
        }

        $filename = $project->getResourcePath($params["resource"]);
        if ($filename === null) {
            throw new NotFoundException;
        }

        $contentType = mime_content_type($filename);
        if ($contentType !== false) {
            header(sprintf("Content-Type: %s", $contentType));
        }

        readfile($filename);
    }

    public function update($params)
    {
        $headers = getallheaders();
        if ($headers === false) {
            throw new ForbiddenException;
        }

        if (!isset($headers["X-Gitlab-Token"])) {
            throw new ForbiddenException;
        }

        $requiredToken = getenv("GITLAB_TOKEN");
        if (!$requiredToken) {
            // $requiredToken is null, false, empty string, ...
            throw new ForbiddenException;
        }

        if ($headers["X-Gitlab-Token"] !== $requiredToken) {
            throw new ForbiddenException;
        }

        $project = $this->projects->byName($params["name"]);

        if ($project === null) {
            throw new NotFoundException;
        }

        if ($project->gitlabCIUseArtifacts and $project->gitlabId !== null) {
            $project->fetchGitlabArtifacts();
            $this->projects->saveSerialized();

            return sprintf("Updated project %s with %d downloads", $project->name, count($project->downloads));
        }

        return "Nothing to update";
    }
}
Pipelines
=========

About
-----

Pipelines are a way to describe, organize and parallelize testing and validation tasks in a *Continuous Integration* (CI) or *Continuous Deployment* (CD) context.


This repository
---------------

This repository is divided in 5 components:

* `Kiboko\Component\JUnitXMLFile`, a JUnit XML results file parser
* `Kiboko\Component\Phroovy`, an interpreter for Jenkins-like pipelines files
* `Kiboko\Component\Pipeline`, a pipelines execution engine, with 2 derived components :
  * `Kiboko\Component\PHPSpecPipeline`
  * `Kiboko\Component\PHPUnitPipeline`
  
Also, there is an OroPlatform bundle: `Kiboko\Bundle\ContinuousIntegrationBundle`

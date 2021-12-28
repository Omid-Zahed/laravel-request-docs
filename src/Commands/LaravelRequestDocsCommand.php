<?php

namespace OmidZahed\LaravelRequestDocs\Commands;

use Illuminate\Console\Command;
use OmidZahed\LaravelRequestDocs\LaravelRequestDocs;

use File;
use OmidZahed\LaravelRequestDocs\SwaggerService\DocToSwagger;


class LaravelRequestDocsCommand extends Command
{
    public $signature = 'lrd:generate5';

    public $description = 'Generate request docs to HTML';

    private $laravelRequestDocs;

    public function __construct(LaravelRequestDocs $laravelRequestDocs)
    {
        $this->laravelRequestDocs = $laravelRequestDocs;
        parent::__construct();
    }

    public function handle()
    {
        $destinationPath = config('request-docs.docs_path') ?? base_path('docs/request-docs/');

        $docs = $this->laravelRequestDocs->getDocs();
        $docs = $this->laravelRequestDocs->sortDocs($docs, config('request-docs.sort_by', 'default'));


        $export_driver=config("request-docs.export_driver","swagger");

        if ($export_driver=="swagger"):
            $file_export_path=config('request-docs.swagger_export_file_path',"./app/swagger");
            $swagger_project_title=config('request-docs.swagger_project_title',env("APP_NAME","app"));
            $swagger_project_version=config('request-docs.swagger_project_version',"0.0.1");

            $doc2swagger=new DocToSwagger($swagger_project_title,$swagger_project_version,$file_export_path);
            $doc2swagger->generate_file($docs);
            $this->comment("generated swagger in path : ".$file_export_path);

        else:
            if (! File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            File::put($destinationPath . '/index.html',
                view('request-docs::index')
                    ->with(compact('docs'))
                    ->render()
            );
            $this->comment("Static HTML generated: $destinationPath");
            endif;





    }
}

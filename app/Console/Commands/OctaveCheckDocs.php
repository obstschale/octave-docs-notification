<?php

namespace App\Console\Commands;

use App\Version;
use Goutte\Client;
use App\Mail\NewOctaveVersion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class OctaveCheckDocs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'octave:check-docs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'See which docs for Octave exist';

    /**
     * Goutte Client instance
     *
     * @var Goutte\Client
     */
    protected $client = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( Client $client )
    {
        parent::__construct();

        $this->client = $client;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $url = 'https://www.gnu.org/software/octave/doc/';
        $this->info( 'Documentation for the following versions are available' );
        $crawler = $this->client->request('GET', $url );

        $crawler->filter('td > a')->each(function ($node) use ($url) {
            if ( starts_with( $node->text(), 'v' ) ) {
                $text = substr( $node->text(), 0, -1 );
                $output = $text . ' (' . $url . $node->attr('href') . ')';

                $v = Version::where('version', $text)->first();

                if ( is_null($v) ) {
                    $newVersion = Version::create(['version' => $text ]);
                    $output = '[new] ' . $output;
                    $this->info( $output );
                    Mail::to('mail@hanshelgebuerger.de')->send(new NewOctaveVersion($newVersion));
                    return;
                }

                $this->line( $output );
            }
        });
    }
}

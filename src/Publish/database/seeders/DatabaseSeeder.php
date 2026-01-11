<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-25 09:13:36
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-12 05:20:56
 * @FilePath: \database\seeders\DatabaseSeeder.php
 */

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use Database\Seeders\LaravelFastApi\LaravelFastApiSeeder;

class DatabaseSeeder extends Seeder
{
    
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
      
       $this->call(
        [
           LaravelFastApiSeeder::class
        ]);
    }
}

<?php

namespace App\Filament\Resources\Attenders\Schemas;

use App\Services\ConsolidatorService;
use App\Traits\HasMemberSearch;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AttenderForm
{
    use HasMemberSearch;
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Member Selection Card
                TextInput::make('Member')
                    ->label('')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('👤 ATTENDER INFORMATION')
                    ->extraAttributes([
                        'style' => 'text-align: center; font-weight: bold; background: #f8fafc; color: #374151; border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px;'
                    ])
                    ->columnSpanFull(),

                TextInput::make('member.first_name')
                    ->label('Member Name')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn ($record) => $record?->member ? ($record->member->first_name . ' ' . $record->member->last_name) : 'No member selected')
                    ->columnSpan(1),

                self::consolidatorSelect()
                    ->columnSpan(1),

                // SUYLN Lessons Section Header
                TextInput::make('suyln_header')
                    ->label('')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('📚 SUYLN LESSONS (10 LESSONS)')
                    ->extraAttributes([
                        'style' => 'text-align: center; font-weight: bold; background: #f0f9ff; color: #0c4a6e; border: 1px solid #bae6fd; border-radius: 8px; padding: 8px;'
                    ])
                    ->columnSpanFull(),

                // SUYLN Lessons - 2 columns x 5 rows
                DatePicker::make('suyln_lesson_1')
                    ->label('📖 Lesson 1: Foundation of Faith')
                    ->placeholder('📅 Select completion date')
                    ->displayFormat('F j, Y')
                    ->helperText('Core principles and beliefs.')
                    ->native(false),
                
                DatePicker::make('suyln_lesson_2')
                    ->label('🙏 Lesson 2: Prayer & Worship')
                    ->placeholder('📅 Select completion date')
                    ->displayFormat('F j, Y')
                    ->helperText('Spiritual disciplines.')
                    ->native(false),
                
                DatePicker::make('suyln_lesson_3')
                    ->label('📚 Lesson 3: Bible Study')
                    ->placeholder('📅 Select completion date')
                    ->displayFormat('F j, Y')
                    ->helperText('Scripture understanding.')
                    ->native(false),

                DatePicker::make('suyln_lesson_4')
                    ->label('💪 Lesson 4: Spiritual Growth')
                    ->placeholder('📅 Select completion date')
                    ->displayFormat('F j, Y')
                    ->helperText('Personal development.')
                    ->native(false),
                
                DatePicker::make('suyln_lesson_5')
                    ->label('🤝 Lesson 5: Fellowship')
                    ->placeholder('📅 Select completion date')
                    ->displayFormat('F j, Y')
                    ->helperText('Community and relationships.')
                    ->native(false),
                
                DatePicker::make('suyln_lesson_6')
                    ->label('💝 Lesson 6: Service')
                    ->placeholder('📅 Select completion date')
                    ->displayFormat('F j, Y')
                    ->helperText('Ministry and giving.')
                    ->native(false),

                DatePicker::make('suyln_lesson_7')
                    ->label('👥 Lesson 7: Leadership')
                    ->placeholder('📅 Select completion date')
                    ->displayFormat('F j, Y')
                    ->helperText('Leading others.')
                    ->native(false),
                
                DatePicker::make('suyln_lesson_8')
                    ->label('📢 Lesson 8: Evangelism')
                    ->placeholder('📅 Select completion date')
                    ->displayFormat('F j, Y')
                    ->helperText('Sharing the Gospel.')
                    ->native(false),
                
                DatePicker::make('suyln_lesson_9')
                    ->label('🌱 Lesson 9: Discipleship')
                    ->placeholder('📅 Select completion date')
                    ->displayFormat('F j, Y')
                    ->helperText('Making disciples.')
                    ->native(false),

                DatePicker::make('suyln_lesson_10')
                    ->label('🎓 Lesson 10: Graduation')
                    ->placeholder('📅 Select completion date')
                    ->displayFormat('F j, Y')
                    ->helperText('Final assessment and completion.')
                    ->native(false),

                // Sunday Service Section Header
                TextInput::make('sunday_service_header')
                    ->label('')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('⛪ SUNDAY SERVICE ATTENDANCE (DCC - 4 SERVICES)')
                    ->extraAttributes([
                        'style' => 'text-align: center; font-weight: bold; background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; border-radius: 8px; padding: 8px;'
                    ])
                    ->columnSpanFull(),

                DatePicker::make('sunday_service_1')
                    ->label('🙏 Sunday Service 1')
                    ->placeholder('📅 Select attendance date')
                    ->displayFormat('F j, Y')
                    ->helperText('First Sunday worship attendance.')
                    ->native(false),

                DatePicker::make('sunday_service_2')
                    ->label('🎵 Sunday Service 2')
                    ->placeholder('📅 Select attendance date')
                    ->displayFormat('F j, Y')
                    ->helperText('Second Sunday worship attendance.')
                    ->native(false),

                DatePicker::make('sunday_service_3')
                    ->label('📖 Sunday Service 3')
                    ->placeholder('📅 Select attendance date')
                    ->displayFormat('F j, Y')
                    ->helperText('Third Sunday worship attendance.')
                    ->native(false),

                DatePicker::make('sunday_service_4')
                    ->label('🌟 Sunday Service 4')
                    ->placeholder('📅 Select attendance date')
                    ->displayFormat('F j, Y')
                    ->helperText('Fourth Sunday worship attendance - Milestone!')
                    ->native(false),

                // Cell Group Section Header
                TextInput::make('cell_group_header')
                    ->label('')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('👥 CELL GROUP ATTENDANCE (CG - 4 MEETINGS)')
                    ->extraAttributes([
                        'style' => 'text-align: center; font-weight: bold; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; border-radius: 8px; padding: 8px;'
                    ])
                    ->columnSpanFull(),


                DatePicker::make('cell_group_1')
                    ->label('🏠 Cell Group Meeting 1')
                    ->placeholder('📅 Select attendance date')
                    ->displayFormat('F j, Y')
                    ->helperText('First cell group meeting attendance.')
                    ->native(false),

                DatePicker::make('cell_group_2')
                    ->label('🤝 Cell Group Meeting 2')
                    ->placeholder('📅 Select attendance date')
                    ->displayFormat('F j, Y')
                    ->helperText('Second cell group meeting attendance.')
                    ->native(false),

                DatePicker::make('cell_group_3')
                    ->label('💬 Cell Group Meeting 3')
                    ->placeholder('📅 Select attendance date')
                    ->displayFormat('F j, Y')
                    ->helperText('Third cell group meeting attendance.')
                    ->native(false),

                DatePicker::make('cell_group_4')
                    ->label('🎯 Cell Group Meeting 4')
                    ->placeholder('📅 Select attendance date')
                    ->displayFormat('F j, Y')
                    ->helperText('Fourth cell group meeting - Fully integrated!')
                    ->native(false),
            ])
            ->columns(2);
    }
}

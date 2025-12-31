<div class="relative mb-6 w-6/7 mx-auto">
    <flux:heading size="xl" level="1">{{ __('How to Play') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Look how to save time when playing') }}</flux:subheading>
    <flux:separator variant="subtle" />


    <div class="flex w-full justify-start gap-2 mt-4 mb-4">
        <flux:button wire:click="back" icon="arrow-left" variant="primary" color="blue">Back to Edit Ticket
        </flux:button>
    </div>

    <div class="space-y-6 w-full max-w-2xl self-center">
        <flux:heading size="lg" level="2">
            {{ __('Valid Bet Sequences:') }}
        </flux:heading>
        <flux:table>
            <flux:table.columns>
                <flux:table.column class="w-1/4">Game Type</flux:table.column>
                <flux:table.column align="center" class="w-1/4">Example</flux:table.column>
                <flux:table.column>What you get</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell variant="strong">BORLET</flux:table.cell>
                    <flux:table.cell align="center" class="font-mono text-slate-600">87</flux:table.cell>
                    <flux:table.cell align="start" class="font-mono text-slate-600">87</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell variant="strong">MARRIAGE</flux:table.cell>
                    <flux:table.cell align="center" class="font-mono text-slate-600">8795</flux:table.cell>
                    <flux:table.cell align="start" class="font-mono text-slate-600">8795</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell variant="strong">MARRIAGE COMB</flux:table.cell>
                    <flux:table.cell align="center" class="font-mono text-slate-600">8795/</flux:table.cell>
                    <flux:table.cell align="start" class="font-mono text-slate-600">8795, 8759, 7895, 7859
                    </flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell variant="strong">LOTTO3</flux:table.cell>
                    <flux:table.cell align="center" class="font-mono text-slate-600">879</flux:table.cell>
                    <flux:table.cell align="start" class="font-mono text-slate-600">879</flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell variant="strong">LOTTO3 BOX</flux:table.cell>
                    <flux:table.cell align="center" class="font-mono text-slate-600">879*</flux:table.cell>
                    <flux:table.cell align="start" class="font-mono text-slate-600">879, 897, 789, 798, 987, 978
                    </flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell variant="strong">LOTTO4 41</flux:table.cell>
                    <flux:table.cell align="center" class="font-mono text-slate-600">8795.1</flux:table.cell>
                    <flux:table.cell align="start" class="font-mono text-slate-600"></flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell variant="strong">LOTTO4 42</flux:table.cell>
                    <flux:table.cell align="center" class="font-mono text-slate-600">8795.2</flux:table.cell>
                    <flux:table.cell align="start" class="font-mono text-slate-600"></flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell variant="strong">LOTTO4 43</flux:table.cell>
                    <flux:table.cell align="center" class="font-mono text-slate-600">8795.3</flux:table.cell>
                    <flux:table.cell align="start" class="font-mono text-slate-600"></flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell variant="strong">LOTTO4 ALL</flux:table.cell>
                    <flux:table.cell align="center" class="font-mono text-slate-600">8795*</flux:table.cell>
                    <flux:table.cell align="start" class="font-mono text-slate-600"></flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell variant="strong">LOTTO5 51</flux:table.cell>
                    <flux:table.cell align="center" class="font-mono text-slate-600">87953.1</flux:table.cell>
                    <flux:table.cell align="start" class="font-mono text-slate-600"></flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell variant="strong">LOTTO5 52</flux:table.cell>
                    <flux:table.cell align="center" class="font-mono text-slate-600">87953.2</flux:table.cell>
                    <flux:table.cell align="start" class="font-mono text-slate-600"></flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell variant="strong">LOTTO5 53</flux:table.cell>
                    <flux:table.cell align="center" class="font-mono text-slate-600">87953.3</flux:table.cell>
                    <flux:table.cell align="start" class="font-mono text-slate-600"></flux:table.cell>
                </flux:table.row>

                <flux:table.row>
                    <flux:table.cell variant="strong">LOTTO5 ALL</flux:table.cell>
                    <flux:table.cell align="center" class="font-mono text-slate-600">87953*</flux:table.cell>
                    <flux:table.cell align="start" class="font-mono text-slate-600"></flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </div>
</div>